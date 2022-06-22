<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Pickup Location (Source) structure
 */
interface PickupLocationInterface extends ExtensibleDataInterface
{
    const KEY_LOCATION_CODE = 'pickup_location_code';
    const KEY_NAME = 'name';
    const KEY_LATITUDE = 'latitude';
    const KEY_LONGITUDE = 'longitude';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_REGION_ID = 'region_id';
    const KEY_REGION = 'region';
    const KEY_CITY = 'city';
    const KEY_STREET = 'street';
    const KEY_POSTCODE = 'postcode';
    const KEY_PHONE = 'telephone';
    const KEY_PICKUP_OPTIONS = 'pickup_options';

    /**
     * Get Pickup Location Code (Source Code)
     *
     * @return string
     */
    public function getLocationCode(): string;

    /**
     * Set Pickup Location Code (Source Code)
     *
     * @param string $sourceCode
     */
    public function setLocationCode(string $sourceCode): void;

    /**
     * Get Pickup Location name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set Pickup Location name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Get Pickup Location latitude
     *
     * @return float|null
     */
    public function getLatitude(): ?float;

    /**
     * Set Pickup Location latitude
     *
     * @param float|null $latitude
     * @return void
     */
    public function setLatitude(?float $latitude): void;

    /**
     * Get Pickup Location longitude
     *
     * @return float|null
     */
    public function getLongitude(): ?float;

    /**
     * Set Pickup Location longitude
     *
     * @param float|null $longitude
     * @return void
     */
    public function setLongitude(?float $longitude): void;

    /**
     * Get Pickup Location country id
     *
     * @return string|null
     */
    public function getCountryId(): ?string;

    /**
     * Set Pickup Location country id
     *
     * @param string|null $countryId
     * @return void
     */
    public function setCountryId(?string $countryId): void;

    /**
     * Get region id if Pickup Location has registered region.
     *
     * @return int|null
     */
    public function getRegionId(): ?int;

    /**
     * Set region id if Pickup Location has registered region.
     *
     * @param int|null $regionId
     * @return void
     */
    public function setRegionId(?int $regionId): void;

    /**
     * Get region title if Pickup Location has custom region
     *
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * Set Pickup Location region title
     *
     * @param string|null $region
     * @return void
     */
    public function setRegion(?string $region): void;

    /**
     * Get Pickup Location city
     *
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * Set Pickup Location city
     *
     * @param string|null $city
     * @return void
     */
    public function setCity(?string $city): void;

    /**
     * Get Pickup Location street name
     *
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * Set Pickup Location street name
     *
     * @param string|null $street
     * @return void
     */
    public function setStreet(?string $street): void;

    /**
     * Get Pickup Location post code
     *
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * Set Pickup Location post code
     *
     * @param string|null $postcode
     * @return void
     */
    public function setPostcode(?string $postcode): void;

    /**
     * Get Pickup Location phone number
     *
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * Set Pickup Location phone number
     *
     * @param string|null $phone
     * @return void
     */
    public function setPhone(?string $phone): void;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationExtensionInterface $extensionAttributes
    ): void;
}
