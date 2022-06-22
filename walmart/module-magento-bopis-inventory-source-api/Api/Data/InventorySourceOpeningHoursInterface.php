<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface InventorySourceOpeningHoursInterface extends ExtensibleDataInterface
{
    const OPEN_HOUR = 'open_hour';
    const CLOSE_HOUR = 'close_hour';
    const DAY_OF_WEEK = 'day_of_week';
    const SOURCE_OPEN_HOURS_ID = 'source_open_hours_id';
    const SOURCE_CODE = 'source_code';
    const INVENTORY_SOURCE_OPENING_HOURS_ID = 'inventory_source_opening_hours_id';

    /**
     * Get inventory_source_opening_hours_id
     *
     * @return string|null
     */
    public function getInventorySourceOpeningHoursId();

    /**
     * Set inventory_source_opening_hours_id
     *
     * @param string $inventorySourceOpeningHoursId
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setInventorySourceOpeningHoursId($inventorySourceOpeningHoursId);

    /**
     * Get source_open_hours_id
     *
     * @return string|null
     */
    public function getSourceOpenHoursId();

    /**
     * Set source_open_hours_id
     *
     * @param string $sourceOpenHoursId
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setSourceOpenHoursId($sourceOpenHoursId);

    /**
     * Get source_code
     *
     * @return string|null
     */
    public function getSourceCode();

    /**
     * Set source_code
     *
     * @param string $sourceCode
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setSourceCode($sourceCode);

    /**
     * Get day_of_week
     *
     * @return string|null
     */
    public function getDayOfWeek();

    /**
     * Set day_of_week
     *
     * @param string $dayOfWeek
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setDayOfWeek($dayOfWeek);

    /**
     * Get open_hour
     *
     * @return string|null
     */
    public function getOpenHour();

    /**
     * Set open_hour
     *
     * @param string $openHour
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setOpenHour($openHour);

    /**
     * Get close_hour
     *
     * @return string|null
     */
    public function getCloseHour();

    /**
     * Set close_hour
     *
     * @param string $closeHour
     *
     * @return \Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setCloseHour($closeHour);

    /**
     * Set Extension Attributes for Pickup Location.
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursExtensionInterface|null $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursExtensionInterface $extensionAttributes
    ): void;

    /**
     * Get Extension Attributes of Pickup Location.
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursExtensionInterface|null
     */
    public function getExtensionAttributes(): ?InventorySourceOpeningHoursExtensionInterface;
}
