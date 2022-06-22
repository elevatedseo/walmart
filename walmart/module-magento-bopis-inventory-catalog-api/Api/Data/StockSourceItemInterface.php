<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemExtensionInterface;

/**
 * Structure of StockSourceItem part
 *
 * @api
 */
interface StockSourceItemInterface extends ExtensibleDataInterface
{
    public const KEY_NAME = 'name';
    public const KEY_SKU = 'sku';
    public const KEY_QTY = 'qty';
    public const KEY_IMAGE_URL = 'image_url';
    public const KEY_OPTIONS = 'options';
    public const KEY_SHIP_TO_STORE = 'ship_to_store';
    public const KEY_ERROR_CODE = 'error_code';

    /**
     * Set Product identity (SKU)
     *
     * @param string $sku
     * @return StockSourceItemInterface
     */
    public function setSku(string $sku): StockSourceItemInterface;

    /**
     * Get Product identity (SKU)
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set Product Name
     *
     * @param string $name
     * @return StockSourceItemInterface
     */
    public function setName(string $name): StockSourceItemInterface;

    /**
     * Get Product Name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set Available Quantity
     *
     * @param float $quantity
     * @return StockSourceItemInterface
     */
    public function setQty(float $quantity): StockSourceItemInterface;

    /**
     * Get Available Quantity
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Set Ship To Store
     *
     * @param int $shipToStore
     * @return StockSourceItemInterface
     */
    public function setShipToStore(int $shipToStore): StockSourceItemInterface;

    /**
     * Get Ship To Store
     *
     * @return int
     */
    public function isShipToStore(): int;

    /**
     * Set Image Url
     *
     * @param string $imageUrl
     * @return StockSourceItemInterface
     */
    public function setImageUrl(string $imageUrl): StockSourceItemInterface;

    /**
     * Get Image Url
     *
     * @return string|null
     */
    public function getImageUrl(): ?string;

    /**
     * Set Product Options
     *
     * @param array $options
     * @return StockSourceItemInterface
     */
    public function setOptions(array $options): StockSourceItemInterface;

    /**
     * Get Options
     *
     * @return string
     */
    public function getOptions();

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string;

    /**
     * @param string $errorCode
     *
     * @return StockSourceItemInterface
     */
    public function setErrorCode(string $errorCode): StockSourceItemInterface;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemExtensionInterface|null
     */
    public function getExtensionAttributes(): ?StockSourceItemExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemExtensionInterface $extensionAttributes
    ): void;
}
