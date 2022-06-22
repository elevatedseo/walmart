<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api;

use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;

/**
 * Get all Active (enabled) Inventory Sources
 */
interface GetEnabledSourcesInterface
{
    /**
     * Get all Active (enabled) Inventory Sources
     *
     * @param array $sourceCodes
     * @return SourceSearchResultsInterface
     */
    public function execute(array $sourceCodes): SourceSearchResultsInterface;
}
