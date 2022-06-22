<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface InventorySourceOpeningHoursSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get inventory_source_opening_hours list.
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface[]
     */
    public function getItems();

    /**
     * Set source_open_hours_id list.
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
