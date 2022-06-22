<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin\Bundle;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryBundleProduct\Model\GetBundleProductStockStatus;
use Walmart\BopisApiConnector\Model\Config as BaseConfiguration;

class GetBundleProductStockStatusPlugin
{
    /**
     * @var BaseConfiguration
     */
    private BaseConfiguration $baseConfiguration;

    public function __construct(
        BaseConfiguration $baseConfiguration
    ) {
        $this->baseConfiguration = $baseConfiguration;
    }

    /**
     * @param GetBundleProductStockStatus $subject
     * @param callable $proceed
     * @param ProductInterface $product
     * @param array $bundleOptions
     * @param int $stockId
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        GetBundleProductStockStatus $subject,
        callable $proceed,
        ProductInterface $product,
        array $bundleOptions,
        int $stockId
    ): bool {
        if (!$this->baseConfiguration->isEnabled()) {
            return $proceed($product, $bundleOptions, $stockId);
        }

        return true;
    }
}
