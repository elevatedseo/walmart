<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin\Order\Handler;

use Magento\InventoryInStorePickupSalesApi\Model\IsStorePickupOrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateAlias;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

class ShipToStoreState
{
    /**
     * States applicable for 'ship_to_store_pending' Order Status
     */
    private const ORDER_STATES = [
        Order::STATE_NEW,
        Order::STATE_PROCESSING
    ];

    /**
     * @var IsShipToStore
     */
    private IsShipToStore $isShipToStoreOrder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param IsShipToStore $isShipToStoreOrder
     * @param Config        $config
     */
    public function __construct(
        IsShipToStore $isShipToStoreOrder,
        Config $config
    ) {
        $this->isShipToStoreOrder = $isShipToStoreOrder;
        $this->config = $config;
    }

    /**
     * @param StateAlias $subject
     * @param $result
     * @param Order $order
     * @return mixed
     */
    public function afterCheck(StateAlias $subject, $result, Order $order)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $currentState = $order->getState();

        if (in_array($currentState, self::ORDER_STATES, false)
            && $this->checkIsStorePickupOrder($order)
            && $this->isShipToStoreOrder->execute($order)
        ) {
            $order->setState(Order::STATE_PROCESSING);
            $order->setStatus(CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE);

            $order->addCommentToStatusHistory(
                __('Order has Ship To Store product(s): %1', implode(',', $this->getShipToStoreSkus($order))),
                $order->getStatus(),
                false
            );
        }

        return $result;
    }

    /**
     * Check is Store Pickup Order
     *
     * @param Order $order
     * @return bool
     */
    private function checkIsStorePickupOrder(Order $order): bool
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes) {
            return (bool)$extensionAttributes->getPickupLocationCode();
        }
        return false;
    }

    /**
     * Get Ship To Store order items
     *
     * @param Order $order
     * @return array
     */
    private function getShipToStoreSkus(Order $order): array
    {
        $result = [];
        foreach ($order->getItems() as $item) {
            if ($item->getExtensionAttributes()->getWmtShipToStore()) {
                $result[] = $item->getSku();
            }
        }

        return $result;
    }
}
