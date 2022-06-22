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

/**
 * Modify order attributes according to store pickup information:
 * - Adds order history comment with Delayed notification information.
 */
class AddDelayedAttributesToOrder
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
        // Add order history item with Delayed information.
        $time = $this->timezone->formatDateTime(new \DateTime(), \IntlDateFormatter::LONG, \IntlDateFormatter::MEDIUM);
        $history = $order->addCommentToStatusHistory(
            __('Order notified as delayed at: %1', $time),
            $order->getStatus(),
            true
        );
        $history->setIsCustomerNotified((int) $order->getExtensionAttributes()->getNotificationSent());
        $this->orderRepository->save($order);
    }
}
