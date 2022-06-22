<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderFaasSync\Api\OrderLineItemsRepositoryInterface;
use Walmart\BopisOrderFaasSync\Model\Email\OrderSyncException;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface;
use Walmart\BopisOrderUpdateApi\Api\DispensedInterface;

class Dispensed extends StatusAction implements DispensedInterface
{
    /**
     * @var OrderInterface
     */
    private OrderInterface $orderInterface;

    /**
     * @var RefundOrderItems
     */
    private RefundOrderItems $refundOrderItems;

    /**
     * @var NotifyOrdersAreDispensed
     */
    private NotifyOrdersAreDispensed $notifyOrdersAreDispensed;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @var OrderSyncException
     */
    private OrderSyncException $orderSyncException;

    /**
     * @var OrderLineItemsRepositoryInterface
     */
    private OrderLineItemsRepositoryInterface $orderLineItemsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OrderInterface $orderInterface
     * @param RefundOrderItems $refundOrderItems
     * @param NotifyOrdersAreDispensed $notifyOrdersAreDispensed
     * @param ResponseInterface $response
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param OrderSyncException $orderSyncException
     * @param OrderLineItemsRepositoryInterface $orderLineItemsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderInterface $orderInterface,
        RefundOrderItems $refundOrderItems,
        NotifyOrdersAreDispensed $notifyOrdersAreDispensed,
        ResponseInterface $response,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderSyncException $orderSyncException,
        OrderLineItemsRepositoryInterface $orderLineItemsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($orderItemRepository);
        $this->orderInterface = $orderInterface;
        $this->refundOrderItems = $refundOrderItems;
        $this->notifyOrdersAreDispensed = $notifyOrdersAreDispensed;
        $this->response = $response;
        $this->orderSyncException = $orderSyncException;
        $this->orderLineItemsRepository = $orderLineItemsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws WebapiException
     */
    public function execute(string $orderId, string $orderSource, $fulfilmentLines, $dispenseSection): ResponseInterface
    {
        $this->response->setSuccess(false);
        $this->response->setMessage('');
        /** @var $order Order */
        $order = $this->orderInterface->loadByIncrementId($orderId);
        $orderId = $order->getId();

        if (!$orderId) {
            $this->response->setMessage((string) __('Order with ID: %1, does not exist', $orderId));
            return $this->getResponse();
        }

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            $this->response->setMessage(
                (string) __('The shipping method is invalid to set order as dispensed.')
            );
            return $this->getResponse();
        }

        $orderStatus = $order->getStatus();

        // In the case we receive multiple order dispensed signal, ignore them
        if ($orderStatus === CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE) {
            $this->response->setMessage(
                (string) __('Order already dispensed')
            );
        }

        // Error recovery scenario imply that we must be able to change the order status to dispensed
        if ($orderStatus !== CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_CODE) {
            /// TODO: We should probably log this somewhere else too
            $order->addCommentToStatusHistory(
                __(
                    'WARNING: Order Dispensed signal received while the order is not in Ready for Pickup status'
                ),
                false,
                false
            );
        }

        try {
            $this->searchCriteriaBuilder
                ->addFilter(OrderLineItemsInterface::ORDER_INCREMENT_ID, $order->getIncrementId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $orderLineItems = $this->orderLineItemsRepository->getList($searchCriteria);
            //save item status
            $this->saveItemsPickedOrDispensedStatus(
                $order,
                $fulfilmentLines,
                self::ACTION_DISPENSED,
                $orderLineItems->getItems()
            );

            $nilPickedItems = $this->getItemsToRefundOrCancel(
                $fulfilmentLines,
                $order->getAllItems(),
                $orderLineItems->getItems());

            if (count($nilPickedItems) > 0) {
                $invoices = $order->getInvoiceCollection();
                if (count($invoices) > 0) {
                    //refund necessary items
                    $this->refundOrderItems->execute($order->getId(), $invoices, $nilPickedItems);
                } else {
                    /**
                     * Obviously we should never come to this point, an order should have already been
                     * invoiced past the dispensed signal but better be safe
                     **/
                    if (!$order->canEdit()) {
                        throw new Exception('Order not yet invoiced but cannot be edited');
                    }
                }
            }

            //notify order is dispensed
            $result = $this->notifyOrdersAreDispensed->execute([$orderId]);
            if ($result->isSuccessful()) {
                foreach ($orderLineItems->getItems() as $lineItem) {
                    $this->orderLineItemsRepository->delete($lineItem);
                }
                $this->response->setMessage(
                    (string) __('The customer has been notified.')
                );
                $this->response->setSuccess(true);
            } else {
                $error = current($result->getErrors());
                $this->response->setMessage((string) $error['message']);
            }
        } catch (Exception $ex) {
            $message = (string) __($ex->getMessage());
            $this->response->setMessage($message);

            $this->orderSyncException->sendOrderSyncExceptionEmail(
                $message,
                $order->getStore()->getWebsiteId(),
                $this->getErrorMessageSubject($order)

            );
        }

        if ($this->response->getSuccess() === false) {
            throw new WebapiException(__($this->response->getMessage()), 0, WebapiException::HTTP_INTERNAL_ERROR);
        }

        return $this->response;
    }

    /**
     * @return ResponseInterface
     * @throws WebapiException
     */
    protected function getResponse(): ResponseInterface
    {
        if ($this->response->getSuccess() === false) {
            throw new WebapiException(__($this->response->getMessage()), 0, WebapiException::HTTP_INTERNAL_ERROR);
        }

        return $this->response;
    }
}
