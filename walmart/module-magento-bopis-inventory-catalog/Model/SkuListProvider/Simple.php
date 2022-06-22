<?php

namespace Walmart\BopisInventoryCatalog\Model\SkuListProvider;

use Walmart\BopisInventoryCatalogApi\Api\SkuListProviderInterface;

class Simple implements SkuListProviderInterface
{
    /**
     * @param string $sku
     * @return string[]
     */
    public function execute(string $sku): array
    {
        return [$sku];
    }
}

