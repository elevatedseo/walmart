<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);
// @codingStandardsIgnoreFile

namespace Walmart\BopisInventorySourceApi\Api;

interface InventorySourceOpeningHoursRepositoryInterface
{

    /**
     * Save inventory_source_opening_hours
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
    );

    /**
     * Retrieve inventory_source_opening_hours
     *
     * @param string $inventorySourceOpeningHoursId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($inventorySourceOpeningHoursId);

    /**
     * Retrieve inventory_source_opening_hours matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve inventory_source_opening_hours list by source_code.
     *
     * @param string $sourceCode
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListBySourceCode(
        string $sourceCode
    );

    /**
     * Delete inventory_source_opening_hours
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
    );

    /**
     * Delete all records of inventory_source_opening_hours by source code
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
     * Delete inventory_source_opening_hours by ID
     *
     * @param string $inventorySourceOpeningHoursId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($inventorySourceOpeningHoursId);
}
