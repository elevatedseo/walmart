<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface InventorySourceParkingSpotRepositoryInterface
{

    /**
     * Save inventory_source_parking_spot
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
    );

    /**
     * Retrieve inventory_source_parking_spot
     *
     * @param string $inventorySourceParkingSpotId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($inventorySourceParkingSpotId);

    /**
     * Retrieve inventory_source_parking_spot matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve inventory_source_parking_spot list by source_code.
     *
     * @param String $sourceCode
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListBySourceCode(
        string $sourceCode
    );

    /**
     * Delete inventory_source_parking_spot
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
    );

    /**
     * Delete all records of inventory_source_parking_spot by source code
     *
     * @param string $sourceCode
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteAllBySourceCode(
        string $sourceCode
    );

    /**
     * Delete inventory_source_parking_spot by ID
     *
     * @param string $inventorySourceParkingSpotId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($inventorySourceParkingSpotId);
}
