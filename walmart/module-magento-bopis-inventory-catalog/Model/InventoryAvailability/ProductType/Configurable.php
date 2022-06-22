<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;

/**
 * Populate product related data for Configurable products
 */
class Configurable extends AbstractType
{
    /**
     * Get Product related data
     *
     * @param ProductInterface $product
     * @param CartItemInterface|null $cartItem
     * @return StockSourceItemInterface
     */
    public function get(ProductInterface $product, CartItemInterface $cartItem = null): StockSourceItemInterface
    {
        $sourceItem = parent::get($product);
        $sourceItem->setOptions($this->getOptions($product));
        return $sourceItem;
    }

    /**
     * Get selected configurable options
     *
     * @param ProductInterface $product
     * @return array
     */
    private function getOptions(ProductInterface $product): array
    {
        $options = $product->getTypeInstance()->getSelectedAttributesInfo($product);
        return $this->formatOptions($options);
    }

    /**
     * Remove needless parts from options
     *
     * @param array $options
     * @return array
     */
    private function formatOptions(array $options): array
    {
        foreach ($options as &$option) {
            array_walk($option, function ($value, $key) use (&$option) {
                if (!in_array($key, ['label', 'value'])) {
                    unset($option[$key]);
                }
            });
        }
        return $options;
    }
}
