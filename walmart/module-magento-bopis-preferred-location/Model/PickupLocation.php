<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationInterface;

class PickupLocation extends AbstractExtensibleModel implements PickupLocationInterface
{
    /**
     * @inheritDoc
     */
    public function getLocationCode(): string
    {
        return $this->_data[self::KEY_LOCATION_CODE];
    }

    /**
     * @inheritDoc
     */
    public function setLocationCode(string $sourceCode): void
    {
        $this->_data[self::KEY_LOCATION_CODE] = $sourceCode;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->_data[self::KEY_NAME];
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): void
    {
        $this->_data[self::KEY_NAME] = $name;
    }

    /**
     * @inheritDoc
     */
    public function getLatitude(): ?float
    {
        return $this->_data[self::KEY_LATITUDE];
    }

    /**
     * @inheritDoc
     */
    public function setLatitude(?float $latitude): void
    {
        $this->_data[self::KEY_LATITUDE] = $latitude;
    }

    /**
     * @inheritDoc
     */
    public function getLongitude(): ?float
    {
        return $this->_data[self::KEY_LONGITUDE];
    }

    /**
     * @inheritDoc
     */
    public function setLongitude(?float $longitude): void
    {
        $this->_data[self::KEY_LONGITUDE] = $longitude;
    }

    /**
     * @inheritDoc
     */
    public function getCountryId(): ?string
    {
        return $this->_data[self::KEY_COUNTRY_ID];
    }

    /**
     * @inheritDoc
     */
    public function setCountryId(?string $countryId): void
    {
        $this->_data[self::KEY_COUNTRY_ID] = $countryId;
    }

    /**
     * @inheritDoc
     */
    public function getRegionId(): ?int
    {
        return $this->_data[self::KEY_REGION_ID];
    }

    /**
     * @inheritDoc
     */
    public function setRegionId(?int $regionId): void
    {
        $this->_data[self::KEY_REGION_ID] = $regionId;
    }

    /**
     * @inheritDoc
     */
    public function getRegion(): ?string
    {
        return $this->_data[self::KEY_REGION];
    }

    /**
     * @inheritDoc
     */
    public function setRegion(?string $region): void
    {
        $this->_data[self::KEY_REGION] = $region;
    }

    /**
     * @inheritDoc
     */
    public function getCity(): ?string
    {
        return $this->_data[self::KEY_CITY];
    }

    /**
     * @inheritDoc
     */
    public function setCity(?string $city): void
    {
        $this->_data[self::KEY_CITY] = $city;
    }

    /**
     * @inheritDoc
     */
    public function getStreet(): ?string
    {
        return $this->_data[self::KEY_STREET];
    }

    /**
     * @inheritDoc
     */
    public function setStreet(?string $street): void
    {
        $this->_data[self::KEY_STREET] = $street;
    }

    /**
     * @inheritDoc
     */
    public function getPostcode(): ?string
    {
        return $this->_data[self::KEY_POSTCODE];
    }

    /**
     * @inheritDoc
     */
    public function setPostcode(?string $postcode): void
    {
        $this->_data[self::KEY_POSTCODE] = $postcode;
    }

    /**
     * @inheritDoc
     */
    public function getPhone(): ?string
    {
        return $this->_data[self::KEY_PHONE];
    }

    /**
     * @inheritDoc
     */
    public function setPhone(?string $phone): void
    {
        $this->_data[self::KEY_PHONE] = $phone;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->_data[self::KEY_PICKUP_OPTIONS];
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): void
    {
        $this->_data[self::KEY_PICKUP_OPTIONS] = $options;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?\Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(PickupLocationInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(\Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
