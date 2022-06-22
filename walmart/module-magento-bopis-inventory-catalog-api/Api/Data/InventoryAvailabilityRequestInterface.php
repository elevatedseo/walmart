<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Request Stock Source in a given SKU and Qty
 *
 * @api
 */
interface InventoryAvailabilityRequestInterface extends ExtensibleDataInterface
{
    /**
     * Set Items
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items): void;

    /**
     * Get requested Items
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterface[]
     */
    public function getItems(): array;
}
