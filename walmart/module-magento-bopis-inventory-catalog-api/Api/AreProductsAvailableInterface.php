<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

interface AreProductsAvailableInterface
{
    /**
     * Get is product available for given SKU
     *
     * @param string[] $skus
     * @param string[] $sources
     * @return bool
     */
    public function execute(array $skus, array $sources): bool;
}
