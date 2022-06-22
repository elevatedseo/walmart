<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\IsProductAvailableCondition;

use Magento\InventoryConfiguration\Model\GetLegacyStockItem;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;

/**
 * Another way (the simplest way) to implement condition for product availability validation
 *
 * Validation of Sources with "Ship to Store" flag enabled
 * assigned to any product type
 */
class Backorder implements AreProductsAvailableInterface
{

    /**
     * @var GetLegacyStockItem
     */
    private GetLegacyStockItem $getLegacyStockItem;

    /**
     * @param GetLegacyStockItem $getLegacyStockItem
     */
    public function __construct(
        GetLegacyStockItem $getLegacyStockItem
    ) {
        $this->getLegacyStockItem = $getLegacyStockItem;
    }

    /**
     * Does all the SKUs allow backorder
     * @param string[] $skus
     * @param string[] $sources
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $skus, array $sources): bool
    {

        /**
         * Quite frankly this is highly unefficient
         */
        foreach($skus as $sku)
        {
            $stockItemConfiguration = $this->getLegacyStockItem->execute($sku);
            if($stockItemConfiguration->getBackorders() !== StockItemConfigurationInterface::BACKORDERS_NO
                && $stockItemConfiguration->getMinQty() >= 0)
            {
                continue;
            }
            return false;
        }

        return true;
    }
}
