<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\CatalogInventory\Model\Configuration;
use Walmart\BopisInventorySourceReservation\Model\GetSourceQuantityWithReservationDataBySku;

/**
 * Get QTY by Stock Source code and with Reservation and Threshold
 */
class GetQtyBySourceAndSku
{
    /**
     * @var Configuration
     */
    private Configuration $inventoryConfig;

    /**
     * @var GetSourceQuantityWithReservationDataBySku
     */
    private GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku;

    /**
     * @param Configuration $inventoryConfig
     * @param GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku
     */
    public function __construct(
        Configuration $inventoryConfig,
        GetSourceQuantityWithReservationDataBySku $quantityWithReservationDataBySku
    ) {
        $this->inventoryConfig = $inventoryConfig;
        $this->quantityWithReservationDataBySku = $quantityWithReservationDataBySku;
    }

    /**
     * @param string $sourceCode
     * @param string $sku
     * @return float
     */
    public function execute(string $sourceCode, string $sku): float
    {
        $thresholdGlobal = $this->inventoryConfig->getStockThresholdQty();
        $stockBySource = $this->quantityWithReservationDataBySku->execute($sku);
        foreach ($stockBySource as $stock) {
            foreach ($stock['sources'] as $source) {
                if ($source['source_code'] == $sourceCode) {
                    $threshold = max((int)$source['threshold_per_item'], (int)$thresholdGlobal);
                    $availability = $source['qty'] - $threshold;
                    return (float)(($availability >= 0) ? $availability : 0);
                }
            }
        }
        return (float)0;
    }
}
