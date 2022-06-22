<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Requested item structure, SKU and QTY
 *
 * @api
 */
interface RequestItemInterface extends ExtensibleDataInterface
{
    public const KEY_SKU = 'sku';
    public const KEY_QTY = 'qty';

    /**
     * Set SKU
     *
     * @param string $sku
     * @return void
     */
    public function setSku(string $sku): void;

    /**
     * Requested SKU
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set Quantity
     *
     * @param float $qty
     * @return void
     */
    public function setQty(float $qty): void;

    /**
     * Requested Product Quantity
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemExtensionInterface|null
     */
    public function getExtensionAttributes(): ?RequestItemExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemExtensionInterface $extensionAttributes
    ): void;
}
