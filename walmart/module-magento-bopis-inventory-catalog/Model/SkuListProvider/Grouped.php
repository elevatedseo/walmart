<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\SkuListProvider;

use Magento\Catalog\Api\Data\LinkInterface;
use Magento\Catalog\Api\ProductLinkManagementInterface;
use Walmart\BopisInventoryCatalogApi\Api\SkuListProviderInterface;

class Grouped implements SkuListProviderInterface
{

    /**
     * @var ProductLinkManagementInterface $productLinkManagement;
     */
    private ProductLinkManagementInterface $productLinkManagement;

    /**
     * @param ProductLinkManagementInterface $linkManagement
     */
    public function __construct(
        ProductLinkManagementInterface $productLinkManagement
    ) {
        $this->productLinkManagement = $productLinkManagement;
    }

    /**
     * @param $sku
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($sku) : array
    {
        $items = $this->productLinkManagement->getLinkedItemsByType($sku, 'associated');

        $skus = [];
        foreach($items as $item)
        {
            $skus[] = $item->getLinkedProductSku();
        }
        return $skus;
    }
}
