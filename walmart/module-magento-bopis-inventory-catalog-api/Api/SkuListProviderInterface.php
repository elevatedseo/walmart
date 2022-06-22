<?php

namespace Walmart\BopisInventoryCatalogApi\Api;

interface SkuListProviderInterface
{
    /**
     * Process a source SKU and return all final SKUs (children) to request inventory against
     * @param string $sku
     * @return string[]
     */
    public function execute(string $sku) : array;
}
