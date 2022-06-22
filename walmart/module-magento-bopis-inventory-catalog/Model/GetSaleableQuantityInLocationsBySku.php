<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Walmart\BopisInventorySourceReservation\Model\GetSourceQuantityWithReservationDataBySku;
use Walmart\BopisInventoryCatalogApi\Api\GetSaleableQuantityInLocationsBySkuInterface;

/**
 * class GetSaleableQuantityInLocationsBySku
 *
 * Return Saleable real quantity by sku including
 *  - Quantity
 *  - Reservation
 *  - Threshold
 */
class GetSaleableQuantityInLocationsBySku implements GetSaleableQuantityInLocationsBySkuInterface
{
    /**
     * @var GetSourceQuantityWithReservationDataBySku
     */
    private GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku
     * @param ProductRepositoryInterface                $productRepository
     */
    public function __construct(
        GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku,
        ProductRepositoryInterface $productRepository
    ) {
        $this->quantityWithReservationDataBySku = $quantityWithReservationDataBySku;
        $this->productRepository = $productRepository;
    }

    /**
     * @param  $locations PickupLocationInterface[]
     * @param  $sku       string
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(array $locations, string $sku): array
    {
        $result = [];
        $stockByStore = $this->quantityWithReservationDataBySku->execute($sku);
        $product = $this->productRepository->get($sku);
        $thresholdGlobal = $product->getExtensionAttributes()->getStockItem()->getData('min_qty');

        foreach ($locations as $location) {
            $inventoryAvailability = $this->getInventoryAvailability($location->getPickupLocationCode(), $stockByStore, $thresholdGlobal);
            $locationData = $this->buildLocationResult($location);
            $locationData['stock_qty'] = $inventoryAvailability;
            $locationData['stock_status'] = ($inventoryAvailability > 0) ? __('In Stock') : __('Out of Stock');
            $result[] = $locationData;
        }
        return $result;
    }

    /**
     * @param  $sourceCode
     * @param  $stockBySource
     * @param  $thresholdGlobal
     * @return int|mixed
     */
    private function getInventoryAvailability($sourceCode, $stockBySource, $thresholdGlobal)
    {
        foreach ($stockBySource as $stock) {
            foreach ($stock['sources'] as $source) {
                if ($source['source_code'] == $sourceCode) {
                    $threshold = max((int)$source['threshold_per_item'], (int)$thresholdGlobal);
                    $availability = $source['qty'] - $threshold;
                    return ( $availability >= 0 ) ? $availability : 0;
                }
            }
        }
        return 0;
    }

    /**
     * Build array with location data
     *
     * @param  $location
     * @return array
     */
    private function buildLocationResult($location): array
    {
        return [
            "pickup_location_code" => $location->getPickupLocationCode(),
            "name" => $location->getName(),
            "latitude" => $location->getLatitude(),
            "longitude"=> $location->getLongitude(),
            "country_id" => $location->getCountryId(),
            "region_id" => $location->getRegionId(),
            "region" => $location->getRegion(),
            "city" => $location->getCity(),
            "street" => $location->getStreet(),
            "postcode" => $location->getPostcode(),
            "phone" => $location->getPhone()
        ];
    }
}
