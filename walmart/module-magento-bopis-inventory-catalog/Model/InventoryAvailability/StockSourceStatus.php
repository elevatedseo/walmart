<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability;

use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceStatusInterface;

/**
 * Class StockSourceStatus
 */
class StockSourceStatus implements StockSourceStatusInterface
{
    /**
     * @var string
     */
    private string $label;

    /**
     * @var string
     */
    private string $code;

    /**
     * @var float
     */
    private float $outOfStockQty;

    /**
     * @param string $label
     * @param string $code
     * @param float $outOfStockQty
     */
    public function __construct(
        string $label,
        string $code,
        float $outOfStockQty = 0.000
    ) {
        $this->label = $label;
        $this->code = $code;
        $this->outOfStockQty = $outOfStockQty;
    }

    /**
     * @inheritDoc
     */
    public function setLabel(string $label): StockSourceStatusInterface
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): StockSourceStatusInterface
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function setOutOfStockQty(float $quantity): StockSourceStatusInterface
    {
        $this->outOfStockQty = $quantity;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutOfStockQty(): float
    {
        return $this->outOfStockQty;
    }
}
