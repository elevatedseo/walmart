<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterface;

interface IsSkuSalableForInStorePickupInterface
{
    /**
     * @param IsProductSalableForRequestedQtyRequestInterface[] $skuRequests
     *
     * @return IsSkuSalableForInStorePickupResultInterface
     */
    public function execute(
        array $skuRequests
    ): IsSkuSalableForInStorePickupResultInterface;
}
