<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api;

/**
 * Send an email to the customer that order is dispensed.
 *
 * @api
 */
interface NotifyOrdersAreDispensedInterface
{
    /**
     * Notify customer that the orders is dispensed.
     *
     * @param int[] $orderIds
     * @return \Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterface
     */
    public function execute(array $orderIds): \Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterface;
}
