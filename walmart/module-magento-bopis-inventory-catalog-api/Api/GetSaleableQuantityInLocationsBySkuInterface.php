<?php

/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;


use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;

/**
 * Service to get saleable quantity in location by skus
 *
 * @api
 */
interface GetSaleableQuantityInLocationsBySkuInterface
{
    /**
     * @param  $locations PickupLocationInterface[]
     * @param  $sku       string
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(array $locations, string $sku): array;
}
