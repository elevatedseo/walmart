<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\ViewModel;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceInterface as InventorySourceInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisBase\Model\Config as BopisBaseConfig;
use Walmart\BopisPreferredLocationApi\Model\Command\GetPickupLocations;

class BopisLocationSelection implements ArgumentInterface
{
    private const XML_PATH_GOOGLE_MAPS_API = 'cataloginventory/source_selection_distance_based_google/api_key';

    /**
     * @var BopisBaseConfig
     */
    private BopisBaseConfig $bopisBaseConfig;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var GetPickupLocations
     */
    private GetPickupLocations $getPickupLocations;

    /**
     * @param BopisBaseConfig $bopisBaseConfig
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority
     * @param StoreManagerInterface $storeManager
     * @param GetPickupLocations $getPickupLocations
     */
    public function __construct(
        BopisBaseConfig $bopisBaseConfig,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        StoreManagerInterface $storeManager,
        GetPickupLocations $getPickupLocations
    ) {
        $this->bopisBaseConfig = $bopisBaseConfig;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->storeManager = $storeManager;
        $this->getPickupLocations = $getPickupLocations;
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getGoogleMapsKey(): ?string
    {
        return $this->bopisBaseConfig->getConfigValue(self::XML_PATH_GOOGLE_MAPS_API);
    }

    /**
     * Return URL google map with APY KEY
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getGoogleMapUrl(): string
    {
        return 'https://www.google.com/maps/embed/v1/place?key=' . $this->getGoogleMapsKey();
    }

    /**
     * Return Locations List
     *
     * @return array
     */
    public function getLocations(): array
    {
        $pickupLocations = $this->getPickupLocations->execute($this->getActiveLocations());
        $locationsArray = [];
        foreach ($pickupLocations as $location) {
            $locationsArray[] = $location->getData();
        }

        return $locationsArray;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getWebSiteCode(): string
    {
        return $this->bopisBaseConfig->getWebSiteCode();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * @return InventorySourceInterface[]
     */
    private function getActiveLocations(): array
    {
        try {
            $stockId = $this->getStockIdForCurrentWebsite->execute();
            $stockSources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

            return array_filter(
                $stockSources,
                static function (SourceInterface $source): bool {
                    return $source->getExtensionAttributes()
                           && (bool)$source->isEnabled()
                           && (bool)$source->getExtensionAttributes()->getIsPickupLocationActive();
                }
            );
        } catch (Exception $exception) {
            return [];
        }
    }
}
