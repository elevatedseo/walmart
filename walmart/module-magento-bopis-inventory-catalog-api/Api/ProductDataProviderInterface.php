<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;

/**
 * Provide data related to product
 */
interface ProductDataProviderInterface
{
    /**
     * Retrieve StockSourceItem data related to product
     *
     * @param string $sku
     * @return StockSourceItemInterface
     * @throws InputException
     * @throws LocalizedException
     */
    public function get(string $sku): StockSourceItemInterface;
}
