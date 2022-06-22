<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\InputException;
use Magento\Quote\Api\Data\CartItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;

/**
 * Populate product related data for Simple products
 */
class Simple extends AbstractType
{
    /**
     * Get Product related data
     *
     * @param ProductInterface $product
     * @param CartItemInterface|null $cartItem
     *
     * @return StockSourceItemInterface
     * @throws InputException
     */
    public function get(ProductInterface $product, CartItemInterface $cartItem = null): StockSourceItemInterface
    {
        return parent::get($product);
    }
}
