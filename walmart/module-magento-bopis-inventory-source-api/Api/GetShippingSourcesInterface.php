<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api;

use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;

interface GetShippingSourcesInterface
{
    /**
     * Get all Inventory Sources with "Use As Shipping Source" flag enabled
     *
     * @return SourceSearchResultsInterface
     */
    public function execute(): SourceSearchResultsInterface;
}
