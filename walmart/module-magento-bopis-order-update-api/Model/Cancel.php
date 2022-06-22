<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Walmart\BopisOrderUpdate\Model\Order\Email\CanceledNotifier;
use Walmart\BopisOrderUpdateApi\Api\CancelInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Service\CreditmemoService;
use Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface;

class Cancel implements CancelInterface
{
    const ORDER_SHIPPING_METHOD_PICKUP_IN_STORE = 'instore_pickup';

    /**
     * @var InvoiceRepositoryInterface
     */
    private InvoiceRepositoryInterface $invoiceRepository;

    /**
     * @var CreditmemoFactory
     */
    private CreditmemoFactory $creditMemoFactory;

    /**
     * @var CreditmemoService
     */
    private CreditmemoService $creditMemoService;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @var OrderManagementInterface
     */
    private OrderManagementInterface $orderManagement;

    /**
     * @var CanceledNotifier
     */
    private CanceledNotifier $canceledNotifier;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param CreditmemoFactory $creditMemoFactory
     * @param CreditmemoService $creditMemoService
     * @param ResponseInterface $response
     * @param OrderManagementInterface $orderManagement
     * @param CanceledNotifier $canceledNotifier
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoFactory          $creditMemoFactory,
        CreditmemoService          $creditMemoService,
        ResponseInterface          $response,
        OrderManagementInterface   $orderManagement,
        CanceledNotifier           $canceledNotifier,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->creditMemoFactory = $creditMemoFactory;
        $this->creditMemoService = $creditMemoService;
        $this->response = $response;
        $this->orderManagement = $orderManagement;
        $this->canceledNotifier = $canceledNotifier;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $increment_id): ResponseInterface
    {
        $this->response->setSuccess(false);
        $this->response->setMessage('');
        $order = $this->getOrder($increment_id);
        if ($order->getEntityId()) {
            if ($order->getShippingMethod() === self::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
                $orderState = $order->getStatus();
                if ($orderState === Order::STATE_CANCELED ||
                    $orderState === Order::STATE_CLOSED ||
                    $orderState === Order::STATE_COMPLETE) {

                    $this->response->setMessage((string)__('Cannot cancel a already completed order'));
                } else {
                    $invoices = $order->getInvoiceCollection();
                    if (count($invoices) > 0) {
                        foreach ($invoices as $invoice) {
                            /** @var $invoice Invoice */
                            $invoice = $this->invoiceRepository->get($invoice->getIncrementId());
                            if ($invoice->canVoid()) {
                                $invoice->void();
                                $order->addCommentToStatusHistory(__('Transaction has been voided'));
                            } else {
                                $creditMemo = $this->creditMemoFactory->createByOrder($order);

                                $creditMemo->setInvoice($invoice);
                                $creditMemo->setCustomerNote((string)__(
                                    'Your Order %1 has been Refunded back in your account',
                                    $order->getIncrementId()
                                ));
                                $creditMemo->setCustomerNoteNotify(false);
                                $creditMemo->addComment(__('Order has been Refunded'));
                                $order->addCommentToStatusHistory(__('Order has been Refunded Successfully'));
                                $this->creditMemoService->refund($creditMemo);
                            }
                        }

                        $this->response->setSuccess(true);

                    } else {
                        if ($order->canCancel()) {
                            $this->orderManagement->cancel($order->getId());
                            $this->canceledNotifier->notify($order);
                            $orderAlternatePickupContact = $this->getOrderAlternatePickupContact($order);
                            if ($orderAlternatePickupContact) {
                                $order->setIsAlternatePickupContact(true);
                                $order->setAlternatePickupContact($orderAlternatePickupContact);
                                $this->canceledNotifier->notify($order);
                            }
                            $this->response->setSuccess(true);
                        } else {
                            $this->response->setMessage(
                                (string)__(
                                    'No Invoices found to refund on Order Id: %1 and unable to cancel',
                                    $increment_id
                                )
                            );
                        }
                    }

                }
            } else {
                $this->response->setMessage((string)__('The shipping method is invalid to process a refund.'));
            }
        } else {
            $this->response->setMessage((string)__('Order with ID: %1, does not exist', $increment_id));
        }

        return $this->response;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderAddressInterface|null
     */
    private function getOrderAlternatePickupContact(OrderInterface $order)
    {
        return $order->getExtensionAttributes()->getPickupContact();
    }

    /**
     * Get Order by Increment ID
     *
     * @param string $incrementId
     * @return OrderInterface
     */
    private function getOrder(string $incrementId): OrderInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $incrementId);
        $orders = $this->orderRepository->getList($searchCriteria->create())->getItems();

        return reset($orders);
    }
}
