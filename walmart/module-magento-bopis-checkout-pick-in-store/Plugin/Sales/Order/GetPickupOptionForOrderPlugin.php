<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Sales\Order;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStore\Model\ResourceModel\OrderPickupOption\GetPickupOptionByOrderId;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Set Pickup Option to Order Entity.
 */
class GetPickupOptionForOrderPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private OrderExtensionFactory $orderExtensionFactory;

    /**
     * @var GetPickupOptionByOrderId
     */
    private GetPickupOptionByOrderId $getPickupOptionByOrderId;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param OrderExtensionFactory    $orderExtensionFactory
     * @param GetPickupOptionByOrderId $getPickupOptionByOrderId
     * @param Config                   $config
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        GetPickupOptionByOrderId $getPickupOptionByOrderId,
        Config $config
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->getPickupOptionByOrderId = $getPickupOptionByOrderId;
        $this->config = $config;
    }

    /**
     * Add Pickup Option extension attribute when loading Order with OrderRepository.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterface $order
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderRepositoryInterface $orderRepository, OrderInterface $order): OrderInterface
    {
        if (!$this->config->isEnabled()) {
            return $order;
        }

        $extension = $order->getExtensionAttributes();

        if (null === $extension) {
            $extension = $this->orderExtensionFactory->create();
        }

        if ($extension->getPickupOption()) {
            return $order;
        }

        $pickupOption = $this->getPickupOptionByOrderId->execute((int)$order->getEntityId());

        if ($pickupOption) {
            $extension->setPickupOption($pickupOption);
        }

        $order->setExtensionAttributes($extension);

        return $order;
    }
}
