<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotExtensionInterface;

class InventorySourceParkingSpot extends AbstractModel implements InventorySourceParkingSpotInterface
{
    /**
     * @var InventorySourceParkingSpotExtensionInterface
     */
    private $extensionAttributes;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceParkingSpot::class);
    }

    /**
     * Get inventory_source_parking_spot_id
     *
     * @return string|null
     */
    public function getInventorySourceParkingSpotId()
    {
        return $this->getData(self::INVENTORY_SOURCE_PARKING_SPOT_ID);
    }

    /**
     * Set inventory_source_parking_spot_id
     *
     * @param string $inventorySourceParkingSpotId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     */
    public function setInventorySourceParkingSpotId($inventorySourceParkingSpotId)
    {
        return $this->setData(self::INVENTORY_SOURCE_PARKING_SPOT_ID, $inventorySourceParkingSpotId);
    }

    /**
     * Get source_parking_spot_id
     *
     * @return string|null
     */
    public function getSourceParkingSpotId()
    {
        return $this->getData(self::SOURCE_PARKING_SPOT_ID);
    }

    /**
     * Set source_parking_spot_id
     *
     * @param string $sourceParkingSpotId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     */
    public function setSourceParkingSpotId($sourceParkingSpotId)
    {
        return $this->setData(self::SOURCE_PARKING_SPOT_ID, $sourceParkingSpotId);
    }

    /**
     * Get source_code
     *
     * @return string|null
     */
    public function getSourceCode()
    {
        return $this->getData(self::SOURCE_CODE);
    }

    /**
     * Set source_code
     *
     * @param string $sourceCode
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    /**
     * Get enabled
     *
     * @return string|null
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     */
    public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?InventorySourceParkingSpotExtensionInterface
    {
        return $this->extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(InventorySourceParkingSpotExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }
}
