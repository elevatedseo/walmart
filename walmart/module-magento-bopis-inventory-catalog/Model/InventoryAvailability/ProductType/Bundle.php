<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\ProductType;

use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\InputException;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Magento\Quote\Api\Data\CartItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterfaceFactory;

/**
 * Populate product related data for Bundle products
 */
class Bundle extends AbstractType
{
    /**
     * @var Configuration
     */
    private Configuration $bundleProductConfiguration;

    /**
     * @inheritDoc
     */
    public function __construct(
        StockSourceItemInterfaceFactory $sourceItemFactory,
        Image $imageHelper,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        ManageStockCondition $isManageStockDisabled,
        Configuration $bundleProductConfiguration
    ) {
        parent::__construct($sourceItemFactory, $imageHelper, $getStockIdForCurrentWebsite, $isManageStockDisabled);
        $this->bundleProductConfiguration = $bundleProductConfiguration;
    }

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
        $sourceItem = parent::get($product);
        $sourceItem->setOptions($this->getOptions($cartItem));
        return $sourceItem;
    }

    /**
     * Get selected configurable options
     *
     * @param CartItemInterface|null $cartItem
     * @return array
     */
    private function getOptions(CartItemInterface $cartItem = null): array
    {
        if ($cartItem) {
            $options = $this->bundleProductConfiguration->getOptions($cartItem);
            return $this->formatOptions($options);
        }
        return [];
    }

    /**
     * Remove needless parts and html tags from options
     *
     * @param array $options
     * @return array
     */
    private function formatOptions(array $options): array
    {
        foreach ($options as &$option) {
            array_walk($option, static function ($value, $key) use (&$option) {
                if (!in_array($key, ['label', 'value'])) {
                    unset($option[$key]);
                } else {
                    $value = is_array($value) ? current($value) : $value;
                    $option[$key] = strip_tags($value);
                }
            });
        }
        return $options;
    }
}
