<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceExtensionInterface;

/**
 * Structure of StockSource part
 *
 * @api
 */
interface StockSourceInterface extends ExtensibleDataInterface
{
    public const IN_STOCK = 'in_stock_items';
    public const OUT_OF_STOCK = 'out_of_stock_items';
    public const STATUS = 'status';

    /**
     * Set Source identity (code)
     *
     * @param string $code
     * @return StockSourceInterface
     */
    public function setSourceCode(string $code): StockSourceInterface;

    /**
     * Get Source Code
     *
     * @return string
     */
    public function getSourceCode(): string;

    /**
     * Set In Stock Items
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface[] $items
     *
     * @return StockSourceInterface
     */
    public function setInStockItems(array $items): StockSourceInterface;

    /**
     * Get In Stock Items
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface[]
     */
    public function getInStockItems(): array;

    /**
     * Set Out Of Stock Items
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface[] $items
     *
     * @return StockSourceInterface
     */
    public function setOutOfStockItems(array $items): StockSourceInterface;

    /**
     * Get Out Of Stock Items
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface[]
     */
    public function getOutOfStockItems(): array;

    /**
     * Get StockSource Summary
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceStatusInterface
     */
    public function getStatus(): StockSourceStatusInterface;

    /**
     * Retrieve existing extension attributes object
     *
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceExtensionInterface|null
     */
    public function getExtensionAttributes(): ?StockSourceExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceExtensionInterface $extensionAttributes
    ): void;
}
