<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Sales\Order;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStore\Model\Order\GetPickupOption;
use Walmart\BopisCheckoutPickInStore\Model\ResourceModel\OrderPickupOption\SaveOrderPickupOption;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class SavePickupOptionForOrderPlugin
{
    /**
     * @var SaveOrderPickupOption
     */
    private SaveOrderPickupOption $saveOrderPickupOption;

    /**
     * @var GetPickupOption
     */
    private GetPickupOption $getPickupOption;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SaveOrderPickupOption $saveOrderPickupOption
     * @param GetPickupOption       $getPickupOption
     * @param Config                $config
     */
    public function __construct(
        SaveOrderPickupOption $saveOrderPickupOption,
        GetPickupOption $getPickupOption,
        Config $config
    ) {
        $this->saveOrderPickupOption = $saveOrderPickupOption;
        $this->getPickupOption = $getPickupOption;
        $this->config = $config;
    }

    /**
     * Save Order to Pickup Option relation when saving the order.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterface $result
     * @param OrderInterface $entity
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $result,
        OrderInterface $entity
    ) {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $pickupOption = $this->getPickupOption->execute($result);

        if ($pickupOption) {
            $this->saveOrderPickupOption->execute((int)$result->getEntityId(), $pickupOption);
        }

        return $result;
    }
}
