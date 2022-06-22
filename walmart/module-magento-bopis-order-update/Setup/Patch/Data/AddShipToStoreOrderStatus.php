<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

/**
 * Add custom "ship_to_store_pending" status to Order States
 */
class AddShipToStoreOrderStatus implements DataPatchInterface
{
    /**
     * @var StatusFactory
     */
    protected StatusFactory $statusFactory;

    /**
     * @var StatusResourceFactory
     */
    protected StatusResourceFactory $statusResourceFactory;

    /**
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE,
            'label' => CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_LABEL,
        ]);

        try {
            $statusResource = $this->statusResourceFactory->create();
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }
        $status->assignState(Order::STATE_PROCESSING, false, true);
        $status->assignState(Order::STATE_NEW, false, true);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
