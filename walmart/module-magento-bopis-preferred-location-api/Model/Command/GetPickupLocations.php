<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Model\Command;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Phrase;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationInterface;
use Walmart\BopisPreferredLocationApi\Api\Data\PickupLocationInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisInventorySourceApi\Model\InventorySource;

/**
 * Sugar class to get PickupLocations objects from provided Sources
 */
class GetPickupLocations
{
    public const IN_STORE_CODE = 'in_store';
    public const CURBSIDE_CODE = 'curbside';

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var PickupLocationInterfaceFactory
     */
    private PickupLocationInterfaceFactory $pickupLocationFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var InventorySource
     */
    private InventorySource $inventorySource;

    /**
     * @param DataObjectHelper               $dataObjectHelper
     * @param PickupLocationInterfaceFactory $pickupLocationFactory
     * @param StoreManagerInterface          $storeManager
     * @param InventorySource                $inventorySource
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        PickupLocationInterfaceFactory $pickupLocationFactory,
        StoreManagerInterface $storeManager,
        InventorySource $inventorySource
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->pickupLocationFactory = $pickupLocationFactory;
        $this->storeManager = $storeManager;
        $this->inventorySource = $inventorySource;
    }

    /**
     * Get array of PickupLocations objects
     *
     * @param SourceInterface[] $locations
     * @return PickupLocationInterface[]
     */
    public function execute(array $locations): array
    {
        $result = [];
        foreach ($locations as $location) {
            $pickupLocation = $this->pickupLocationFactory->create();
            $pickupLocation->setLocationCode($location->getSourceCode());

            // remove needless data
            $sourceData = $location->getData();
            unset($sourceData['extension_attributes']);

            $this->dataObjectHelper->populateWithArray(
                $pickupLocation,
                $sourceData,
                PickupLocationInterface::class
            );

            $websiteId = (int) $this->storeManager->getStore()->getWebsiteId();

            $pickupLocation->setOptions($this->getOptionsByLocation($location, $websiteId));
            $pickupLocation->setEstimatedPickupTime($this->getEstimatedPickupTimeByLocation($location, $websiteId));
            $pickupLocation->setPickupTimeDisclaimer($this->inventorySource->getPickupTimeDisclaimer($websiteId));
            $result[$pickupLocation->getLocationCode()] = $pickupLocation;
        }

        return $result;
    }

    /**
     * Get Pickup Location Options
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return array
     */
    public function getOptionsByLocation(SourceInterface $location, int $websiteId): array
    {
        $options = [];
        if ($this->inventorySource->isInStorePickupEnabled($location, $websiteId)) {
            array_push($options, [
                'code' => self::IN_STORE_CODE,
                'title' => $this->inventorySource->getConfig()->getInStorePickupTitle($websiteId),
                'description' => $this->inventorySource->getStorePickupInstructions($location, $websiteId)
            ]);
        }

        if ($this->inventorySource->isCurbsideEnabled($location, $websiteId)) {
            array_push($options, [
                'code' => self::CURBSIDE_CODE,
                'title' => $this->inventorySource->getConfig()->getCurbsideTitle($websiteId),
                'description' => $this->inventorySource->getCurbsideInstructions($location, $websiteId)
            ]);
        }

        return $options;
    }

    /**
     * Get Estimated Pickup Time Message
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return string
     */
    public function getEstimatedPickupTimeByLocation(SourceInterface $location, int $websiteId): string
    {
        $estimatedPickupLeadTime = $this->inventorySource->getPickupLeadTime($location, $websiteId);

        $estimatedPickupTimeLabel = $this->inventorySource->getPickupTimeLabel($location, $websiteId);

        return __($estimatedPickupTimeLabel, $estimatedPickupLeadTime)->render();
    }
}
