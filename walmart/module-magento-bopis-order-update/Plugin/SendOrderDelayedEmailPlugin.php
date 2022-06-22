<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdate\Model\Order\Email\DelayedNotifier;
use Walmart\BopisOrderUpdate\Model\Order\AddDelayedAttributesToOrder;

class SendOrderDelayedEmailPlugin
{
    /**
     * @var IsShipToStore
     */
    private IsShipToStore $isShipToStoreOrder;

    /**
     * @var DelayedNotifier
     */
    private DelayedNotifier $delayedNotifier;

    /**
     * @var AddDelayedAttributesToOrder
     */
    private AddDelayedAttributesToOrder $addDelayedAttributesToOrder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param IsShipToStore               $isShipToStoreOrder
     * @param DelayedNotifier             $delayedNotifier
     * @param AddDelayedAttributesToOrder $addDelayedAttributesToOrder
     * @param Config                      $config
     */
    public function __construct(
        IsShipToStore $isShipToStoreOrder,
        DelayedNotifier $delayedNotifier,
        AddDelayedAttributesToOrder $addDelayedAttributesToOrder,
        Config $config
    ) {
        $this->isShipToStoreOrder = $isShipToStoreOrder;
        $this->delayedNotifier = $delayedNotifier;
        $this->addDelayedAttributesToOrder = $addDelayedAttributesToOrder;
        $this->config = $config;
    }

    /**
     * @param OrderSender $subject
     * @param             $result
     * @param Order       $order
     *
     * @return mixed
     * @throws \Exception
     */
    public function afterSend(OrderSender $subject, $result, Order $order)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($this->checkIsStorePickupOrder($order) && $this->isShipToStoreOrder->execute($order)) {
            $this->delayedNotifier->notify($order);
            $this->addDelayedAttributesToOrder->execute($order);
        }

        return $result;
    }

    /**
     * Check is Store Pickup Order
     *
     * @param Order $order
     *
     * @return bool
     */
    private function checkIsStorePickupOrder(Order $order): bool
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes) {
            return (bool) $extensionAttributes->getPickupLocationCode();
        }

        return false;
    }
}
