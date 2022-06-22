<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Walmart\BopisInventoryCatalogApi\Api\IsStorePickupAvailableForSkusInterface;

/**
 * StorePickup availability for product
 */
class IsStorePickupAvailableForSkus implements IsStorePickupAvailableForSkusInterface
{
    /**
     * @var StorePickupResolver
     */
    private StorePickupResolver $storePickupResolver;

    /**
     * @param StorePickupResolver $storePickupResolver
     */
    public function __construct(StorePickupResolver $storePickupResolver)
    {
        $this->storePickupResolver = $storePickupResolver;
    }

    /**
     * @inheirtDoc
     */
    public function execute(array $skus, array $sources = []): array
    {
        $toReturn = [];
        foreach ($skus as $sku) {
            $toReturn[$sku] =  $this->storePickupResolver->isAvailableForSkus([$sku], $sources);
        }
        return [$toReturn];
    }
}
