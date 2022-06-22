<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestExtensionInterface;

/**
 * Requested item structure, SKU and QTY
 *
 * @api
 */
interface ItemRequestInterface extends ExtensibleDataInterface
{
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
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ItemRequestExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestExtensionInterface $extensionAttributes
    ): void;
}
