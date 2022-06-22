<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;
use Magento\Sales\Model\Order;

class UpdateOrderStatuses implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->addNewOrderStatusAndAsignToState(
            CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_CODE,
            CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_LABEL,
            Order::STATE_PROCESSING
        );

        $this->addNewOrderStatusAndAsignToState(
            CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE,
            CustomOrderStateInterface::ORDER_STATUS_DISPENSED_LABEL,
            Order::STATE_COMPLETE
        );
    }

    /**
     *  Create new order status and assign it to the existent state
     *
     * @param $newStatusCode
     * @param $newStatusLabel
     * @param $stateToAssign
     */
    protected function addNewOrderStatusAndAsignToState($newStatusCode, $newStatusLabel, $stateToAssign)
    {
        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status' => $newStatusCode, 'label' => $newStatusLabel]
        );

        $states = [
            [
                'status'     => $newStatusCode,
                'state'      => $stateToAssign,
                'is_default' => 0,
                'visible_on_front' => 1,
            ]
        ];

        foreach ($states as $state) {
            $this->moduleDataSetup->getConnection()->insertOnDuplicate(
                $this->moduleDataSetup->getTable('sales_order_status_state'),
                $state
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
