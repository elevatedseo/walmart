<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Structure of Result Item (out of stock)
 *
 * @api
 */
interface ResultItemInterface extends ExtensibleDataInterface
{
    public const KEY_NAME = 'name';
    public const KEY_SKU = 'sku';
    public const KEY_QTY = 'qty';
    public const KEY_IMAGE_URL = 'image_url';
    public const KEY_OPTIONS = 'options';
    public const KEY_ERROR_CODE = 'error_code';

    /**
     * Set Product identity (SKU)
     *
     * @param string $sku
     * @return ResultItemInterface
     */
    public function setSku(string $sku): ResultItemInterface;

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
     * @return ResultItemInterface
     */
    public function setName(string $name): ResultItemInterface;

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
     * @return ResultItemInterface
     */
    public function setQty(float $quantity): ResultItemInterface;

    /**
     * Get Available Quantity
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Set Image Url
     *
     * @param string $imageUrl
     * @return ResultItemInterface
     */
    public function setImageUrl(string $imageUrl): ResultItemInterface;

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
     * @return ResultItemInterface
     */
    public function setOptions(array $options): ResultItemInterface;

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
     * @return ResultItemInterface
     */
    public function setErrorCode(string $errorCode): ResultItemInterface;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ResultItemExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemExtensionInterface $extensionAttributes
    ): void;
}
