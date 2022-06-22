<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Plugin\InventoryApi\SourceRepository;

use Magento\Framework\DataObject;
use Magento\InventoryApi\Api\Data\SourceExtensionInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface as Location;

/**
 * Set data to Source itself from its extension attributes to save these values to `inventory_source` DB table.
 */
class SaveStorePickupPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Persist the In-Store pickup attribute on Source save
     *
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface           $source
     *
     * @return                                        array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ): array {
        if (!$this->config->isEnabled()) {
            return [$source];
        }

        if (!$source instanceof DataObject) {
            return [$source];
        }

        $extensionAttributes = $source->getExtensionAttributes();

        if ($extensionAttributes !== null) {
            // @codingStandardsIgnoreStart
            $source->setData(Location::ALLOW_SHIP_TO_STORE, $extensionAttributes->getAllowShipToStore());
            $source->setData(Location::USE_AS_SHIPPING_SOURCE, $extensionAttributes->getUseAsShippingSource());
            $source->setData(Location::STORE_PICKUP_ENABLED, $extensionAttributes->getStorePickupEnabled());
            $source->setData(Location::CURBSIDE_ENABLED, $extensionAttributes->getCurbsideEnabled());
            $source->setData(Location::PICKUP_LEAD_TIME, $extensionAttributes->getPickupLeadTime());
            $source->setData(Location::PICKUP_TIME_LABEL, $extensionAttributes->getPickupTimeLabel());
            $source->setData(Location::TIMEZONE, $extensionAttributes->getTimezone());
            $source->setData(Location::STORE_PICKUP_INSTRUCTIONS, $extensionAttributes->getStorePickupInstructions());
            $source->setData(Location::CURBSIDE_INSTRUCTIONS, $extensionAttributes->getCurbsideInstructions());
            $source->setData(Location::PARKING_SPOTS_ENABLED, $extensionAttributes->getParkingSpotsEnabled());
            $source->setData(Location::PARKING_SPOT_MANDATORY, $extensionAttributes->getParkingSpotMandatory());
            $source->setData(Location::CUSTOM_PARKING_SPOT_ENABLED, $extensionAttributes->getCustomParkingSpotEnabled());
            $source->setData(Location::USE_CAR_COLOR, $extensionAttributes->getUseCarColor());
            $source->setData(Location::CAR_COLOR_MANDATORY, $extensionAttributes->getCarColorMandatory());
            $source->setData(Location::USE_CAR_MAKE, $extensionAttributes->getUseCarMake());
            $source->setData(Location::CAR_MAKE_MANDATORY, $extensionAttributes->getCarMakeMandatory());
            $source->setData(Location::USE_ADDITIONAL_INFORMATION, $extensionAttributes->getUseAdditionalInformation());
            $source->setData(Location::ADDITIONAL_INFORMATION_MANDATORY, $extensionAttributes->getAdditionalInformationMandatory());
            // @codingStandardsIgnoreEnd
        }

        return [$source];
    }
}
