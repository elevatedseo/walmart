<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface InventorySourceParkingSpotInterface extends ExtensibleDataInterface
{
    const ENABLED = 'enabled';
    const SOURCE_PARKING_SPOT_ID = 'source_parking_spot_id';
    const INVENTORY_SOURCE_PARKING_SPOT_ID = 'inventory_source_parking_spot_id';
    const SOURCE_CODE = 'source_code';
    const NAME = 'name';

    /**
     * Get inventory_source_parking_spot_id
     *
     * @return string|null
     */
    public function getInventorySourceParkingSpotId();

    /**
     * Set inventory_source_parking_spot_id
     *
     * @param string $inventorySourceParkingSpotId
     *
     * @return \Api\Data\InventorySourceParkingSpotInterface
     */
    public function setInventorySourceParkingSpotId($inventorySourceParkingSpotId);

    /**
     * Get source_parking_spot_id
     *
     * @return string|null
     */
    public function getSourceParkingSpotId();

    /**
     * Set source_parking_spot_id
     *
     * @param string $sourceParkingSpotId
     *
     * @return \Api\Data\InventorySourceParkingSpotInterface
     */
    public function setSourceParkingSpotId($sourceParkingSpotId);

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
     * @return \Api\Data\InventorySourceParkingSpotInterface
     */
    public function setSourceCode($sourceCode);

    /**
     * Get enabled
     *
     * @return string|null
     */
    public function getEnabled();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return \Api\Data\InventorySourceParkingSpotInterface
     */
    public function setEnabled($enabled);

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \Api\Data\InventorySourceParkingSpotInterface
     */
    public function setName($name);

    /**
     * Set Extension Attributes for Pickup Location.
     *
     * @param \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotExtensionInterface|null $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotExtensionInterface $extensionAttributes
    ): void;

    /**
     * Get Extension Attributes of Pickup Location.
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotExtensionInterface|null
     */
    public function getExtensionAttributes(): ?InventorySourceParkingSpotExtensionInterface;
}
