<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterface;

/**
 * Service to provide In Stock and Out of Stock items
 * and Inventory Availability status for requested products sources
 *
 * @api
 */
interface InventoryAvailabilityServiceInterface
{
    /**
     * Request InventoryAvailability information for SKU and QTY
     *
     * Example:
     * {
     *     "request": {
     *         "items": [
     *             {
     *                 "sku": "24-MB01",
     *                 "qty": 4
     *             },
     *             {
     *                 "sku": "24-MB03",
     *                 "qty": 2
     *             }
     *         ]
     *     }
     * }
     *
     * @param InventoryAvailabilityRequestInterface $request
     * @param bool $collectCartData
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterface
     */
    public function execute(InventoryAvailabilityRequestInterface $request, bool $collectCartData = true): InventoryAvailabilityResultInterface;
}
