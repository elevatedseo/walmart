<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\Source;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\DataObject;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface;

/**
 * Set store-pickup related source extension attributes
 */
class InitPickupLocationExtensionAttributes
{
    /**
     * @var ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(ExtensionAttributesFactory $extensionAttributesFactory)
    {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Set store-pickup related source extension attributes.
     *
     * @param SourceInterface $source
     */
    public function execute(SourceInterface $source): void
    {
        if (!$source instanceof DataObject) {
            return;
        }
        $allowShipToStore = $source->getData(PickupLocationInterface::ALLOW_SHIP_TO_STORE);
        $useAsShippingSource = $source->getData(PickupLocationInterface::USE_AS_SHIPPING_SOURCE);
        $storePickupEnabled = $source->getData(PickupLocationInterface::STORE_PICKUP_ENABLED);
        $curbsideEnabled = $source->getData(PickupLocationInterface::CURBSIDE_ENABLED);
        $pickupLeadTime = $source->getData(PickupLocationInterface::PICKUP_LEAD_TIME);
        $pickupTimeLabel = $source->getData(PickupLocationInterface::PICKUP_TIME_LABEL);
        $timezone = $source->getData(PickupLocationInterface::TIMEZONE);
        $storePickupInstructions = $source->getData(PickupLocationInterface::STORE_PICKUP_INSTRUCTIONS);
        $curbsideInstructions = $source->getData(PickupLocationInterface::CURBSIDE_INSTRUCTIONS);
        $parkingSpotsEnabled = $source->getData(PickupLocationInterface::PARKING_SPOTS_ENABLED);
        $parkingSpotMandatory = $source->getData(PickupLocationInterface::PARKING_SPOT_MANDATORY);
        $customParkingSpotEnabled = $source->getData(PickupLocationInterface::CUSTOM_PARKING_SPOT_ENABLED);
        $useCarColor = $source->getData(PickupLocationInterface::USE_CAR_COLOR);
        $carColorMandatory = $source->getData(PickupLocationInterface::CAR_COLOR_MANDATORY);
        $useCarMake = $source->getData(PickupLocationInterface::USE_CAR_MAKE);
        $carMakeMandatory = $source->getData(PickupLocationInterface::CAR_MAKE_MANDATORY);
        $useAdditionalInformation = $source->getData(PickupLocationInterface::USE_ADDITIONAL_INFORMATION);
        $additionalInfoMandatory = $source->getData(PickupLocationInterface::ADDITIONAL_INFORMATION_MANDATORY);

        $extensionAttributes = $source->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(SourceInterface::class);
            /**
 * @noinspection PhpParamsInspection
*/
            $source->setExtensionAttributes($extensionAttributes);
        }

        $extensionAttributes
            ->setAllowShipToStore((bool)$allowShipToStore)
            ->setUseAsShippingSource((bool)$useAsShippingSource)
            ->setStorePickupEnabled((bool)$storePickupEnabled)
            ->setCurbsideEnabled((bool)$curbsideEnabled)
            ->setPickupLeadTime($pickupLeadTime)
            ->setPickupTimeLabel($pickupTimeLabel)
            ->setTimezone($timezone)
            ->setStorePickupInstructions($storePickupInstructions)
            ->setCurbsideInstructions($curbsideInstructions)
            ->setParkingSpotsEnabled((bool)$parkingSpotsEnabled)
            ->setParkingSpotMandatory((bool)$parkingSpotMandatory)
            ->setCustomParkingSpotEnabled((bool)$customParkingSpotEnabled)
            ->setUseCarColor((bool)$useCarColor)
            ->setCarColorMandatory((bool)$carColorMandatory)
            ->setUseCarMake((bool)$useCarMake)
            ->setCarMakeMandatory((bool)$carMakeMandatory)
            ->setUseAdditionalInformation((bool)$useAdditionalInformation)
            ->setAdditionalInformationMandatory((bool)$additionalInfoMandatory);
    }
}
