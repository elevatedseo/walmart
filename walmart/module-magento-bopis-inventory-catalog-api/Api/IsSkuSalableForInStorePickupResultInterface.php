<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

interface IsSkuSalableForInStorePickupResultInterface
{
    public const IS_SALABLE = 'is_salable';
    public const IS_SHIP_TO_STORE = 'is_ship_to_store';

    /**
     * @return bool
     */
    public function isSalable(): bool;

    /**
     * @param bool $isSalable
     *
     * @return $this
     */
    public function setIsSalable(bool $isSalable): self;

    /**
     * @return bool
     */
    public function isShipToStore(): bool;

    /**
     * @param bool $isShipToStore
     *
     * @return $this
     */
    public function setIsShipToStore(bool $isShipToStore): self;
}
