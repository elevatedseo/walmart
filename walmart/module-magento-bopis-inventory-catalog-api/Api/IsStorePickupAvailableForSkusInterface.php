<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

interface IsStorePickupAvailableForSkusInterface
{
    /**
     * Get is product available for StorePickup
     *
     * @param string[] $sku
     * @param string[] $sources
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\AssociativeArrayItemInterface[]
     */
    public function execute(array $skus, array $sources = []): array;
}
