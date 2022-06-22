<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

/**
 * Structure of StockSourceStatus part
 *
 * @api
 */
interface StockSourceStatusInterface
{
    public const IN_STOCK = 'in_stock';
    public const OUT_OF_STOCK = 'out_of_stock';
    public const PARTIALLY_STOCKED = 'partially_stocked';

    /**
     * Set Product Label
     *
     * @param string $label
     * @return StockSourceStatusInterface
     */
    public function setLabel(string $label): StockSourceStatusInterface;

    /**
     * Get Product Label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Set Status Code
     *
     * @param string $code
     * @return StockSourceStatusInterface
     */
    public function setCode(string $code): StockSourceStatusInterface;

    /**
     * Get Status Code
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Set Out Of Stock Quantity
     *
     * @param float $quantity
     * @return StockSourceStatusInterface
     */
    public function setOutOfStockQty(float $quantity): StockSourceStatusInterface;

    /**
     * Get Out Of Stock Quantity
     *
     * @return float
     */
    public function getOutOfStockQty(): float;
}
