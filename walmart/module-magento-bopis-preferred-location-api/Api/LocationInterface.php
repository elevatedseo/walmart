<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Api;

/**
 * Provide information about bopis locations
 * Interface LocationInterface
 *
 * @api
 */
interface LocationInterface
{
    /**
     * @param string $searchTerm
     * @param string $scopeCode ex. base
     * @param string $scopeType ex. website
     * @param string|null $sku
     * @param string|null $latitude
     * @param string|null $longitude
     *
     * @return \Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @api
     */
    public function get(
        string $searchTerm,
        string $scopeCode,
        string $scopeType,
        ?string $sku = null,
        ?string $latitude = null,
        ?string $longitude = null
    ): array;
}
