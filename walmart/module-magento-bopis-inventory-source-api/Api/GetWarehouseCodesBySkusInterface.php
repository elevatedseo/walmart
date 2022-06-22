<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api;

/**
 * Sugar service to get list of Stock Sources for Home Delivery
 */
interface GetWarehouseCodesBySkusInterface
{
    /**
     * @param array $skus
     * @param bool $shipFromStoreEnabled
     * @return array
     */
    public function execute(array $skus, bool $shipFromStoreEnabled = true): array;
}
