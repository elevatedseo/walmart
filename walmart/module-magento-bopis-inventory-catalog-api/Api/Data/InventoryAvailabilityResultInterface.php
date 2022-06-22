<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultExtensionInterface;

/**
 * Result of requested SKU and QTY grouped by Stock Sources
 *
 * @api
 */
interface InventoryAvailabilityResultInterface extends ExtensibleDataInterface
{
    /**
     * Inventory Availability response
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterface[]
     */
    public function getSourceList(): array;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultExtensionInterface|null
     */
    public function getExtensionAttributes(): ?InventoryAvailabilityResultExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultExtensionInterface $extensionAttributes
    ): void;
}
