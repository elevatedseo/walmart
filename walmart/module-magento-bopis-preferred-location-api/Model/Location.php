<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Model;

use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterfaceFactory;
use Magento\InventoryInStorePickup\Model\ResourceModel\Source\GetOrderedDistanceToSources;
use Magento\InventoryInStorePickup\Model\SearchRequest;
use Magento\InventoryInStorePickup\Model\SearchRequest\Area;
use Magento\InventoryInStorePickupApi\Api\GetPickupLocationsInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventoryCatalogApi\Api\GetSaleableQuantityInLocationsBySkuInterface;
use Walmart\BopisPreferredLocationApi\Api\LocationInterface;

/**
 * Provide info about Bopis Locations
 * Class Location
 */
class Location implements LocationInterface
{
    const CARRIERS_INSTORE_SEARCH_RADIUS = 'carriers/instore/search_radius';

    /**
     * @var GetPickupLocationsInterface
     */
    private GetPickupLocationsInterface $getPickupLocations;

    /**
     * @var Config
     */
    private Config $bopisConfig;

    /**
     * @var GetSaleableQuantityInLocationsBySku
     */
    private GetSaleableQuantityInLocationsBySkuInterface $getSaleableQuantityInLocationsBySku;

    /**
     * @var GetOrderedDistanceToSources
     */
    private GetOrderedDistanceToSources $getOrderedDistanceToSources;

    /**
     * @var LatLngInterfaceFactory
     */
    private LatLngInterfaceFactory $latLngFactory;

    /**
     * @param GetPickupLocationsInterface $getPickupLocations
     * @param Config $bopisConfig
     * @param GetSaleableQuantityInLocationsBySkuInterface $getSaleableQuantityInLocationsBySku
     * @param GetOrderedDistanceToSources $getOrderedDistanceToSources
     * @param LatLngInterfaceFactory $latLngFactory
     */
    public function __construct(
        GetPickupLocationsInterface $getPickupLocations,
        Config $bopisConfig,
        GetSaleableQuantityInLocationsBySkuInterface $getSaleableQuantityInLocationsBySku,
        GetOrderedDistanceToSources $getOrderedDistanceToSources,
        LatLngInterfaceFactory $latLngFactory
    ) {
        $this->getPickupLocations = $getPickupLocations;
        $this->bopisConfig = $bopisConfig;
        $this->getSaleableQuantityInLocationsBySku = $getSaleableQuantityInLocationsBySku;
        $this->getOrderedDistanceToSources = $getOrderedDistanceToSources;
        $this->latLngFactory = $latLngFactory;
    }

    /**
     * @inheirtDoc
     */
    public function get(
        string $searchTerm,
        string $scopeCode,
        string $scopeType,
        ?string $sku = null,
        ?string $latitude = null,
        ?string $longitude = null
    ): array
    {
        $radius = $this->bopisConfig->getConfigValue(self::CARRIERS_INSTORE_SEARCH_RADIUS);
        $sources = [];

        // search for the sources in the specific radius
        if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
            $latLng = $this->latLngFactory->create([
                'lat' => (float)$latitude,
                'lng' => (float)$longitude,
            ]);

            $sources = array_keys($this->getOrderedDistanceToSources->execute([$latLng], (int)$radius));
        }

        $area = !empty($searchTerm) ? new Area((int)$radius, $searchTerm) : null;
        $searchRequest  = new SearchRequest($scopeCode, $scopeType, $area);
        $sourceInventoryResult = $this->getPickupLocations->execute($searchRequest);

        $locations = $sourceInventoryResult->getItems();
        // filter closest sources
        if (!empty($sources)) {
            $locations = array_filter($locations, static function ($location) use ($sources) {
                return in_array($location->getPickupLocationCode(), $sources, true);
            });
        }

        if (empty($sku)) {
            return $locations;
        }

        return $this->getSaleableQuantityInLocationsBySku->execute($locations, $sku);
    }
}
