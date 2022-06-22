<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Result of requested SKU and QTY grouped by Stock Sources
 *
 * @api
 */
interface ResultInterface extends ExtensibleDataInterface
{
    public const KEY_OUT_OF_STOCK_ITEMS = 'out_of_stock_items';
    public const KEY_RESULT = 'result';

    /**
     * Set list of Out Of Stock items
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface[] $items
     * @return ResultInterface
     */
    public function setOutOfStockItems(array $items): ResultInterface;

    /**
     * Add Out Of Stock item to the list
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface $item
     * @return ResultInterface
     */
    public function addOutOfStockItem(ResultItemInterface $item): ResultInterface;

    /**
     * Get list of Out Of Stock items
     *
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface[]
     */
    public function getOutOfStockItems(): array;

    /**
     * Set is Home Delivery available for requested items
     *
     * @param int $isAvailable
     * @return ResultInterface
     */
    public function setResult(int $isAvailable): ResultInterface;

    /**
     * Get is Home Delivery available for requested items
     *
     * @return int
     */
    public function getResult(): int;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\ResultExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ResultExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\ResultExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisHomeDeliveryApi\Api\Data\ResultExtensionInterface $extensionAttributes
    ): void;
}
