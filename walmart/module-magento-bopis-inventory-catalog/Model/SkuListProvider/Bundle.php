<?php

namespace Walmart\BopisInventoryCatalog\Model\SkuListProvider;

use Magento\Bundle\Api\ProductLinkManagementInterface;
use Walmart\BopisInventoryCatalogApi\Api\SkuListProviderInterface;

class Bundle implements SkuListProviderInterface
{
    /**
     * @var ProductLinkManagementInterface $productLinkManagement
     */
    private ProductLinkManagementInterface $productLinkManagement;

    /**
     * @param ProductLinkManagementInterface $productLinkManagement
     */
    public function __construct(
        ProductLinkManagementInterface $productLinkManagement
    )
    {
        $this->productLinkManagement = $productLinkManagement;
    }


    /**
     * @param string $sku
     * @return string[]
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(string $sku): array
    {
        $items = $this->productLinkManagement->getChildren($sku);
        $skus = [];
        foreach ($items as $item) {
            $skus[] = $item['sku'];
        }
        return $skus;
    }
}

