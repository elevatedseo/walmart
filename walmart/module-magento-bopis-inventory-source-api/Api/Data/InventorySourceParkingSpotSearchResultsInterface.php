<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface InventorySourceParkingSpotSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get inventory_source_parking_spot list.
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface[]
     */
    public function getItems();

    /**
     * Set source_parking_spot_id list.
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
