<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Walmart\BopisOrderFaasSync\Api\OrderLineItemsRepositoryInterface;
use Walmart\BopisOrderFaasSync\Model\Email\OrderSyncException;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface;
use Walmart\BopisOrderUpdateApi\Api\PickedInterface;

class Picked extends StatusAction implements PickedInterface
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
     * @var NotifyOrdersAreReadyForPickup
     */
    private NotifyOrdersAreReadyForPickup $notifyOrdersAreReadyForPickup;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @var InvoiceService
     */
    private InvoiceService $invoiceService;

    /**
     * @var OrderManagementInterface
     */
    private OrderManagementInterface $orderManagement;

    /**
     * @var InvoiceRepositoryInterface
     */
    private InvoiceRepositoryInterface $invoiceRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ItemRepository
     */

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
     * @param NotifyOrdersAreReadyForPickup $notifyOrdersAreReadyForPickup
     * @param ResponseInterface $response
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param InvoiceService $invoiceService
     * @param OrderManagementInterface $orderManagement
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderSyncException $orderSyncException
     * @param OrderLineItemsRepositoryInterface $orderLineItemsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderInterface $orderInterface,
        RefundOrderItems $refundOrderItems,
        NotifyOrdersAreReadyForPickup $notifyOrdersAreReadyForPickup,
        ResponseInterface $response,
        OrderItemRepositoryInterface $orderItemRepository,
        InvoiceService $invoiceService,
        OrderManagementInterface $orderManagement,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        OrderSyncException $orderSyncException,
        OrderLineItemsRepositoryInterface $orderLineItemsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($orderItemRepository);
        $this->orderInterface = $orderInterface;
        $this->refundOrderItems = $refundOrderItems;
        $this->notifyOrdersAreReadyForPickup = $notifyOrdersAreReadyForPickup;
        $this->response = $response;
        $this->invoiceService = $invoiceService;
        $this->orderManagement = $orderManagement;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->orderSyncException = $orderSyncException;
        $this->orderLineItemsRepository = $orderLineItemsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws WebapiException
     */
    public function execute(string $orderId, string $orderSource, $fulfilmentLines, $pickingSection): ResponseInterface
    {
        $this->response->setSuccess(false);
        $this->response->setMessage('');

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $orderId)->create();
        $orderList = $this->orderRepository->getList($searchCriteria);

        /** @var OrderInterface $order */
        $order = current($orderList->getItems());

        if (!$order) {
            $this->response->setMessage((string) __('Order with ID: %1, does not exist', $orderId));
            return $this->getResponse();
        }

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            $this->response->setMessage(
                (string) __('The shipping method is invalid to set order as ready for pickup.')
            );
            return $this->getResponse();
        }

        if ($order->getState() !== Order::STATE_PROCESSING) {
            $this->response->setMessage(
                (string) __(
                    'Order state invalid, just orders with processing state can be marked as ready for pickup.'
                )
            );
            return $this->getResponse();
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
                self::ACTION_PICKED,
                $orderLineItems->getItems()
            );
            $authOnly = false;
            $nilPickedItems = $this->getItemsToRefundOrCancel($fulfilmentLines, $order->getAllItems(), $orderLineItems->getItems());
            // Check if the order can be invoiced
            if ($order->canInvoice()) {
                // If the order can be invoiced get the picked order items
                $pickedItems = $this->getPickedItems($fulfilmentLines, $order->getAllItems(), $orderLineItems->getItems());

                $parentPickedItems = [];
                foreach($pickedItems as $lineOrderItemId => $qty)
                {
                    $orderLineItem = $this->orderItemRepository->get($lineOrderItemId);
                    $parent = $orderLineItem->getParentItem();
                    if($parent && $parent->getProductType() == Configurable::TYPE_CODE)
                    {
                        $parentPickedItems[$parent->getItemId()] = $qty;
                    }
                }
                foreach($parentPickedItems as $id => $qty)
                {
                    $pickedItems[$id] = $qty;
                }

                foreach($order->getAllItems() as $item)
                {
                    if($item->getIsVirtual())
                    {
                        $pickedItems[$item->getItemId()] = $item->getQtyToInvoice();
                    }
                }

                // Invoice only the $fulfilmentLines with status picked
                $invoice = $this->invoiceService->prepareInvoice($order, $pickedItems);
                // capture online if payment allows it
                if ($invoice->canCapture()) {
                    $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
                }
                $invoice->register();
                $this->invoiceRepository->save($invoice);
                // recalculate totals after creating the invoice
                $this->orderRepository->save($order);

                $authOnly = true;
            }

            if (count($nilPickedItems) > 0) {
                if ($authOnly) {
                    $this->orderManagement->cancel($order->getId());

                } else {
                    $invoices = $order->getInvoiceCollection();
                    if (count($invoices) === 0) {
                        throw new Exception('Order not yet invoiced but cannot be canceled');
                    }

                    //refund necessary items
                    try {
                        $this->refundOrderItems->execute($order->getId(), $invoices, $nilPickedItems);
                        foreach ($orderLineItems->getItems() as $lineItem) {
                            if (isset($nilPickedItems[$lineItem->getOrderItemId()])) {
                                $orderItem = $order->getItemById($lineItem->getOrderItemId());
                                $refundedOrderItems = (int)$nilPickedItems[$lineItem->getOrderItemId()]['qty_to_refund'];
                                if ($refundedOrderItems === (int)$orderItem->getQtyOrdered()) {
                                    $this->orderLineItemsRepository->delete($lineItem);
                                }
                            }
                        }
                    } catch (LocalizedException $exception) {
                        $message = (string) __(
                            'There was a problem with refunding the order: %1', $exception->getMessage()
                        );

                        $order->addCommentToStatusHistory($message);
                        $order->setStatus(Order::STATE_PAYMENT_REVIEW);
                        $this->orderRepository->save($order);

                        $this->orderSyncException->sendOrderSyncExceptionEmail(
                            $message,
                            $order->getStore()->getWebsiteId(),
                            $this->getErrorMessageSubject($order)
                        );

                        $this->response->setMessage($message);
                        $this->response->setSuccess(false);

                        return $this->getResponse();
                    }
                }
            }

            //notify order is ready for pickup
            $result = $this->notifyOrdersAreReadyForPickup->execute([$order->getId()]);
            if ($result->isSuccessful()) {
                $this->response->setMessage(
                    (string) __('The customer has been notified.')
                );
                $this->response->setSuccess(true);
                return $this->getResponse();
            }

            $error = current($result->getErrors());
            $this->response->setMessage((string) $error['message']);
            return $this->getResponse();
        } catch (Exception $ex) {
            $message = (string) __($ex->getMessage());
            $this->response->setMessage($message);

            $this->orderSyncException->sendOrderSyncExceptionEmail(
                $message,
                $order->getStore()->getWebsiteId(),
                $this->getErrorMessageSubject($order)
            );
        }

        return $this->getResponse();
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
