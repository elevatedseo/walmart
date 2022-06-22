<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\InputException;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Magento\Quote\Api\Data\CartItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterfaceFactory;

/**
 * Add general product information to InventoryAvailability response
 */
abstract class AbstractType
{
    public const EMPTY_IMAGE_VALUE = 'no_selection';

    /**
     * @var StockSourceItemInterfaceFactory
     */
    protected StockSourceItemInterfaceFactory $sourceItemFactory;

    /**
     * @var Image
     */
    private Image $imageHelper;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;

    /**
     * @var ManageStockCondition
     */
    private ManageStockCondition $isManageStockDisabled;

    /**
     * @param StockSourceItemInterfaceFactory $sourceItemFactory
     * @param Image $imageHelper
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param ManageStockCondition $isManageStockDisabled
     */
    public function __construct(
        StockSourceItemInterfaceFactory $sourceItemFactory,
        Image $imageHelper,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        ManageStockCondition $isManageStockDisabled
    ) {
        $this->sourceItemFactory = $sourceItemFactory;
        $this->imageHelper = $imageHelper;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->isManageStockDisabled = $isManageStockDisabled;
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
        if ($this->isVirtual($product)) {
            throw new InputException(__('Unable to get virtual product stock item information'));
        }

        return $this->sourceItemFactory->create([
            'data' => [
                StockSourceItemInterface::KEY_SKU => $product->getSku(),
                StockSourceItemInterface::KEY_NAME => $product->getName(),
                StockSourceItemInterface::KEY_QTY => 0,
                StockSourceItemInterface::KEY_OPTIONS => [],
                StockSourceItemInterface::KEY_IMAGE_URL => $this->getProductImageUrl($product)
            ]
        ]);
    }

    /**
     * Get Product Image URL
     *
     * @param ProductInterface $product
     * @return string
     */
    private function getProductImageUrl(ProductInterface $product): string
    {
        $image = $product->getImage() ?? $product->getThumbnail();
        if (!$image || $image === self::EMPTY_IMAGE_VALUE) {
            return '';
        }

        $imageHelper = $this->imageHelper->init(
            $product,
            'product_thumbnail_image'
        )->setImageFile($image);

        return $imageHelper->getUrl();
    }

    /**
     * @param ProductInterface|Product $product
     *
     * @return bool
     */
    private function isVirtual(ProductInterface $product): bool
    {
        if (!$product->getIsVirtual()) {
            return false;
        }

        $stockId = $this->getStockIdForCurrentWebsite->execute();
        return !$this->isManageStockDisabled->execute($product->getSku(), $stockId);
    }
}
