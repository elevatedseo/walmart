<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order;

use Magento\InventoryInStorePickupSalesApi\Model\IsStorePickupOrderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

/**
 * Modify order attributes according to store pickup information:
 * - Adds order history comment with Dispensed notification information.
 * - Changes order status to "Dispensed".
 */
class AddDispensedAttributesToOrder
{
    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var IsStorePickupOrderInterface
     */
    private IsStorePickupOrderInterface $isStorePickupOrder;

    /**
     * @param IsStorePickupOrderInterface $isStorePickupOrder
     * @param OrderRepositoryInterface    $orderRepository
     * @param TimezoneInterface           $timezone
     */
    public function __construct(
        IsStorePickupOrderInterface $isStorePickupOrder,
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone
    ) {
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
        $this->isStorePickupOrder = $isStorePickupOrder;
    }

    /**
     * Modify order attributes according to store pickup information.
     *
     * @param OrderInterface $order
     *
     * @return void
     * @throws \Exception
     */
    public function execute(OrderInterface $order): void
    {
        // Change order status to "Dispensed".
        if ($order->getEntityId()
            && $this->isStorePickupOrder->execute((int) $order->getEntityId())
        ) {
            $order->setState(Order::STATE_COMPLETE);
            $order->setStatus(CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE);
        }

        // Add order history item with Dispensed information.
        $time = $this->timezone->formatDateTime(new \DateTime(), \IntlDateFormatter::LONG, \IntlDateFormatter::MEDIUM);
        $history = $order->addCommentToStatusHistory(
            __('Order notified as dispensed at: %1', $time),
            $order->getStatus(),
            true
        );
        $history->setIsCustomerNotified((int) $order->getExtensionAttributes()->getNotificationSent());
        $this->orderRepository->save($order);
    }
}
