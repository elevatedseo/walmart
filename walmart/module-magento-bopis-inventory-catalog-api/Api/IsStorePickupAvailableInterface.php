<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

interface IsStorePickupAvailableInterface
{
    /**
     * Get is product available for StorePickup
     *
     * @param string $sku
     * @param string[] $sources
     * @return bool
     */
    public function execute(string $sku, array $sources = []): bool;
}
