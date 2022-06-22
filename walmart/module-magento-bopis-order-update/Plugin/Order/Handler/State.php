<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin\Order\Handler;

use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateAlias;
use Magento\Sales\Model\Order;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;
use Magento\InventoryInStorePickupSalesApi\Model\IsStorePickupOrderInterface;

class State
{
    /**
     * @var IsStorePickupOrderInterface
     */
    private IsStorePickupOrderInterface $isStorePickupOrder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param IsStorePickupOrderInterface $isStorePickupOrder
     * @param Config                      $config
     */
    public function __construct(
        IsStorePickupOrderInterface $isStorePickupOrder,
        Config $config
    ) {
        $this->isStorePickupOrder = $isStorePickupOrder;
        $this->config = $config;
    }

    /**
     * @param StateAlias $subject
     * @param $result
     * @param Order      $order
     *
     * @return mixed
     */
    public function afterCheck(StateAlias $subject, $result, Order $order)
    {
        if (!$this->config->isEnabled()
            || $order->getIsVirtual()
        ) {
            return $result;
        }

        $currentState = $order->getState();
        $currentStatus = $order->getStatus();

        //prevent the order status to going to complete if is in store pickup
        if ($currentState === Order::STATE_COMPLETE
            && $currentStatus != CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE
            && !$order->canShip()
            && $this->isStorePickupOrder->execute(
                (int) $order->getEntityId()
            )
        ) {
            $order->setState(Order::STATE_PROCESSING);
            $order->setStatus(CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_CODE);
        }

        return $result;
    }
}
