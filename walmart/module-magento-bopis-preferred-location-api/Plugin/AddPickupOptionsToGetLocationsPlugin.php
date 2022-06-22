<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Plugin;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\Store\Model\StoreManagerInterface;
use Throwable;
use Walmart\BopisInventorySourceApi\Model\Source\Command\GetEnabledSources;
use Walmart\BopisPreferredLocationApi\Model\Command\GetPickupLocations;
use Walmart\BopisPreferredLocationApi\Model\Location;

/**
 * Add Pickup Options to PickupLocation
 *
 * @see \Walmart\BopisPreferredLocationApi\Api\LocationInterface
 */
class AddPickupOptionsToGetLocationsPlugin
{
    /**
     * @var GetPickupLocations
     */
    private GetPickupLocations $getPickupLocations;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private ExtensibleDataObjectConverter $extensibleDataObjectConverter;

    /**
     * @var GetEnabledSources
     */
    private GetEnabledSources $getEnabledSources;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param GetPickupLocations $getPickupLocations
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param GetEnabledSources $getEnabledSources
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetPickupLocations $getPickupLocations,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        GetEnabledSources $getEnabledSources,
        StoreManagerInterface $storeManager
    ) {
        $this->getPickupLocations = $getPickupLocations;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->getEnabledSources = $getEnabledSources;
        $this->storeManager = $storeManager;
    }

    /**
     * Add Pickup Options to SearchResult or Array
     *
     * @param Location $subject
     * @param array $result
     * @param string $searchTerm
     * @param string $scopeCode
     * @param string $scopeType
     * @param string|null $sku
     * @param string|null $latitude
     * @param string|null $longitude
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGet(
        Location $subject,
        array $result,
        string $searchTerm,
        string $scopeCode,
        string $scopeType,
        ?string $sku = null,
        ?string $latitude = null,
        ?string $longitude = null
    ): array {
        if (empty($result)) {
            return $result;
        }

        $extendedResult = [];
        foreach ($result as $location) {
            if ($location instanceof PickupLocationInterface) {
                $pickupLocationData = $this->extensibleDataObjectConverter->toNestedArray(
                    $location,
                    [],
                    PickupLocationInterface::class
                );
            } else {
                $pickupLocationData = $location;
            }
            $source = $this->retrieveSourceByCode($pickupLocationData['pickup_location_code']);
            $pickupLocationData['pickup_options'] = $this->getPickupLocations->getOptionsByLocation(
                $source,
                $this->getWebsiteId()
            );
            $extendedResult[] = $pickupLocationData;
        }

        return $extendedResult;
    }


    /**
     * Get Current Website ID
     *
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteId(): int
    {
        return (int)$this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Enabled Inventory Source by Source Code
     *
     * @param string $getPickupLocationCode
     * @return SourceInterface|null
     */
    private function retrieveSourceByCode(string $getPickupLocationCode): ?SourceInterface
    {
        try {
            $source = $this->getEnabledSources->execute([$getPickupLocationCode]);
            return current($source->getItems());
        } catch (Throwable $e) {
            return null;
        }
    }
}
