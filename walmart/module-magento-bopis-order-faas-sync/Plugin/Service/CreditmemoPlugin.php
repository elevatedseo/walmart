<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\CreditmemoService;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterfaceFactory;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOrderFaasSync\Model\State;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

class CreditmemoPlugin
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $bopisQueueRepository;

    /**
     * @var BopisQueueInterfaceFactory
     */
    private BopisQueueInterfaceFactory $bopisQueueInterfaceFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var OrderItemRepositoryInterface
     */
    private OrderItemRepositoryInterface $orderItemRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param BopisQueueRepositoryInterface $bopisQueueRepository
     * @param BopisQueueInterfaceFactory    $bopisQueueInterfaceFactory
     * @param OrderRepositoryInterface      $orderRepository
     * @param OrderItemRepositoryInterface  $orderItemRepository
     * @param Config                        $config
     */
    public function __construct(
        BopisQueueRepositoryInterface $bopisQueueRepository,
        BopisQueueInterfaceFactory $bopisQueueInterfaceFactory,
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        Config $config
    ) {
        $this->bopisQueueRepository = $bopisQueueRepository;
        $this->bopisQueueInterfaceFactory = $bopisQueueInterfaceFactory;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->config = $config;
    }

    /**
     * @param CreditmemoService $subject
     * @param callable $proceed
     * @param CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     *
     * @return void
     * @throws LocalizedException
     */
    public function aroundRefund(
        CreditmemoService $subject,
        callable $proceed,
        CreditmemoInterface $creditmemo,
        $offlineRequested = false
    ) {
        if (!$this->config->isEnabled()) {
            return $proceed($creditmemo, $offlineRequested);
        }

        if ($creditmemo->getOrder()->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return $proceed($creditmemo, $offlineRequested);
        }

        if ($creditmemo->getOrder()->getStatus() === CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE
            || $creditmemo->getOrder()->getStatus() === CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE
            || $creditmemo->getOrder()->getState() === Order::STATE_COMPLETE
            || $creditmemo->getOrder()->getState() === State::PENDING_CANCELLATION) {
            return $proceed($creditmemo, $offlineRequested);
        }

        if ($creditmemo->getOrder()->getState() === State::ACKNOWLEDGED_CANCELLATION
            && $creditmemo->getOrder()->getStatus() === Order::STATE_PAYMENT_REVIEW
        ) {
            return $proceed($creditmemo, $offlineRequested);
        }

        foreach ($creditmemo->getItems() as $item) {
            $orderItem = $this->orderItemRepository->get($item->getOrderItemId());
            if ((int)$item->getQty() !== (int)$orderItem->getQtyOrdered()) {
                throw new LocalizedException(__('Partial refund is not supported.'));
            }
        }

        /** @var BopisQueueInterface $queue */
        $queue = $this->bopisQueueInterfaceFactory->create();
        $queue->setStatus(Status::NOT_SENT);
        $queue->setEntityType(EntityType::ENTITY_TYPE_ORDER);
        $queue->setEntityId($creditmemo->getOrderId());
        $queue->setOperationType(OperationTypeInterface::CANCEL_ORDER);
        $this->bopisQueueRepository->save($queue);

        $order = $creditmemo->getOrder();
        $order->addCommentToStatusHistory(
            __('Order added to the BOPIS queue. Waiting on sending the cancellation request.')
        );
        $order->setState(State::PENDING_CANCELLATION);
        $this->orderRepository->save($order);

        return $creditmemo;
    }
}
