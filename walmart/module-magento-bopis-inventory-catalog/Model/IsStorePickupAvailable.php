<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Walmart\BopisInventoryCatalogApi\Api\IsStorePickupAvailableInterface;

/**
 * StorePickup availability for product
 */
class IsStorePickupAvailable implements IsStorePickupAvailableInterface
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
    public function execute(string $sku, array $sources = []): bool
    {
        return $this->storePickupResolver->isAvailableForSkus([$sku], $sources);
    }
}
