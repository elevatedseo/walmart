<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursExtensionInterface;

class InventorySourceOpeningHours extends AbstractModel implements InventorySourceOpeningHoursInterface
{
    /**
     * @var InventorySourceOpeningHoursExtensionInterface
     */
    private $extensionAttributes;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceOpeningHours::class);
    }

    /**
     * Get inventory_source_opening_hours_id
     *
     * @return string|null
     */
    public function getInventorySourceOpeningHoursId()
    {
        return $this->getData(self::INVENTORY_SOURCE_OPENING_HOURS_ID);
    }

    /**
     * Set inventory_source_opening_hours_id
     *
     * @param string $inventorySourceOpeningHoursId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setInventorySourceOpeningHoursId($inventorySourceOpeningHoursId)
    {
        return $this->setData(self::INVENTORY_SOURCE_OPENING_HOURS_ID, $inventorySourceOpeningHoursId);
    }

    /**
     * Get source_open_hours_id
     *
     * @return string|null
     */
    public function getSourceOpenHoursId()
    {
        return $this->getData(self::SOURCE_OPEN_HOURS_ID);
    }

    /**
     * Set source_open_hours_id
     *
     * @param string $sourceOpenHoursId
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setSourceOpenHoursId($sourceOpenHoursId)
    {
        return $this->setData(self::SOURCE_OPEN_HOURS_ID, $sourceOpenHoursId);
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
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    /**
     * Get day_of_week
     *
     * @return string|null
     */
    public function getDayOfWeek()
    {
        return $this->getData(self::DAY_OF_WEEK);
    }

    /**
     * Set day_of_week
     *
     * @param string $dayOfWeek
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setDayOfWeek($dayOfWeek)
    {
        return $this->setData(self::DAY_OF_WEEK, $dayOfWeek);
    }

    /**
     * Get open_hour
     *
     * @return string|null
     */
    public function getOpenHour()
    {
        return $this->getData(self::OPEN_HOUR);
    }

    /**
     * Set open_hour
     *
     * @param string $openHour
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setOpenHour($openHour)
    {
        return $this->setData(self::OPEN_HOUR, $openHour);
    }

    /**
     * Get close_hour
     *
     * @return string|null
     */
    public function getCloseHour()
    {
        return $this->getData(self::CLOSE_HOUR);
    }

    /**
     * Set close_hour
     *
     * @param string $closeHour
     *
     * @return \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface
     */
    public function setCloseHour($closeHour)
    {
        return $this->setData(self::CLOSE_HOUR, $closeHour);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?InventorySourceOpeningHoursExtensionInterface
    {
        return $this->extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(InventorySourceOpeningHoursExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }
}
