<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader;
use Magento\Sales\Model\Order;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOrderFaasSync\Api\CancelConfirmedInterface;
use Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface;
use Walmart\BopisOrderFaasSync\Api\ConfirmationStatusInterface;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderFaasSync\Api\OrderLineItemsRepositoryInterface;
use Walmart\BopisOrderUpdate\Model\Order\Email\CanceledNotifier;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

class CancelConfirmed implements CancelConfirmedInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var OrderInterfaceFactory
     */
    private OrderInterfaceFactory $orderFactory;

    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var CreditmemoLoader
     */
    private CreditmemoLoader $creditmemoLoader;

    /**
     * @var CreditmemoManagementInterface
     */
    private CreditmemoManagementInterface $creditmemoManagement;

    /**
     * @var CanceledNotifier
     */
    private CanceledNotifier $canceledNotifier;

    /**
     * @var OrderLineItemsRepositoryInterface
     */
    private OrderLineItemsRepositoryInterface $orderLineItemsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterfaceFactory $orderFactory
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param CreditmemoLoader $creditmemoLoader
     * @param CreditmemoManagementInterface $creditmemoManagement
     * @param CanceledNotifier $canceledNotifier
     * @param Logger $logger
     * @param OrderLineItemsRepositoryInterface $orderLineItemsRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderInterfaceFactory $orderFactory,
        BopisQueueRepositoryInterface $queueRepository,
        CreditmemoLoader $creditmemoLoader,
        CreditmemoManagementInterface $creditmemoManagement,
        CanceledNotifier $canceledNotifier,
        Logger $logger,
        OrderLineItemsRepositoryInterface $orderLineItemsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->queueRepository = $queueRepository;
        $this->logger = $logger;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoManagement = $creditmemoManagement;
        $this->canceledNotifier = $canceledNotifier;
        $this->orderLineItemsRepository = $orderLineItemsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws \Exception
     */
    public function execute(
        string $orderId,
        ConfirmationStatusInterface $status
    ): void {
        $this->logger->info(
            'Received Order Cancellation ACK',
            [
                'orderId' => $orderId,
                'status' => $status->getDescription()
            ]
        );

        /** @var Order $order */
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if (!$order->getId()) {
            $message = __("The entity that was requested doesn't exist. Verify the entity and try again.");
            $this->logger->error('Order Cancellation ACK: ' . $message);

            throw new Exception(
                $message,
                null,
                Exception::HTTP_NOT_FOUND
            );
        }

        if ($order->getState() !== State::PENDING_CANCELLATION) {
            $message = __('Incorrect Order state. Expected: %1', State::PENDING_CANCELLATION);
            $this->logger->error('Order Cancellation ACK: ' . $message);

            throw new Exception(
                $message,
                null,
                Exception::HTTP_BAD_REQUEST
            );
        }

        if ($status->getDescription() !== ReasonInterface::STATUS_CANCELLED) {
            $order->addCommentToStatusHistory(
                $this->getErrorMessage($status)
            );
            $this->orderRepository->save($order);
            return;
        }

        try {
            $hasInvoice = $order->getInvoiceCollection()->getSize() > 0;
            $refundFailed = false;
            $sendCancelEmail = $order->getStatus() !== CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE;
            if ($hasInvoice) {
                try {
                    // do the refund of there is an invoice
                    $creditmemo = $this->getCreditmemo((int)$order->getId());
                    if ($creditmemo) {
                        $creditmemo->setInvoice($order->getInvoiceCollection()->getFirstItem());
                        $this->creditmemoManagement->refund($creditmemo);
                    }
                } catch (LocalizedException $exception) {
                    $order->addCommentToStatusHistory(
                        'There was a problem with refunding the order: ' . $exception->getMessage()
                    );
                    $refundFailed = true;
                }
            }

            $order->addCommentToStatusHistory(
                __('Received Cancel acknowledgment from the BOPIS solution.')
            );
            $order->setState(State::ACKNOWLEDGED_CANCELLATION);
            $order->cancel();

            $order->setStatus($refundFailed ? Order::STATE_PAYMENT_REVIEW : Order::STATE_CLOSED);
            // reset state after cancellation
            $order->setState(State::ACKNOWLEDGED_CANCELLATION);

            $this->orderRepository->save($order);

            $this->searchCriteriaBuilder
                ->addFilter(OrderLineItemsInterface::ORDER_INCREMENT_ID, $order->getIncrementId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $orderLineItems = $this->orderLineItemsRepository->getList($searchCriteria);

            foreach ($orderLineItems->getItems() as $lineItem) {
                $this->orderLineItemsRepository->delete($lineItem);
            }

            if ($sendCancelEmail) {
                $this->canceledNotifier->notify($order);
            }
        } catch (\Exception $exception) {
            $this->logger->error('Order Cancellation ACK: ' . $exception->getMessage());

            $order->addCommentToStatusHistory(
                'There was a problem with cancelling the order:' . $exception->getMessage()
            );
            $this->orderRepository->save($order);
            throw $exception;
        }

        try {
            $queueItem = $this->queueRepository->getByOrderId(
                $order->getId(),
                OperationTypeInterface::CANCEL_ORDER
            );
            $queueItem->setStatus(Status::COMPLETED);
            $this->queueRepository->save($queueItem);
        } catch (NoSuchEntityException $exception) {
            $this->logger->error(
                "Queue Item for selected order doesn't exist",
                [
                    'increment_id' => $orderId
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                'There was a problem with changing the status of the queue item.',
                [
                    'increment_id' => $orderId,
                    'msg' => $exception->getMessage()
                ]
            );
        }
    }

    /**
     * @param ConfirmationStatusInterface $status
     *
     * @return string
     */
    private function getErrorMessage(ConfirmationStatusInterface $status): string
    {
        $error = 'There was a problem with the BOPIS order cancellation.';
        if ($status->getReason() && $status->getReason()->getDescription()) {
            $error .= ' Description: ' . $status->getReason()->getDescription();
        }

        return $error;
    }

    /**
     * @param int $orderId
     *
     * @return CreditmemoInterface|null
     */
    private function getCreditmemo(int $orderId): ?CreditmemoInterface
    {
        $this->creditmemoLoader->setOrderId($orderId);

        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            return $creditmemo;
        }

        return null;
    }
}
