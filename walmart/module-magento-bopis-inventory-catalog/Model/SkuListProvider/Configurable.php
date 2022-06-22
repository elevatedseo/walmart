<?php

namespace Walmart\BopisInventoryCatalog\Model\SkuListProvider;

use Magento\ConfigurableProduct\Api\Data\LinkInterface;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use Walmart\BopisInventoryCatalogApi\Api\SkuListProviderInterface;


class Configurable implements SkuListProviderInterface
{
    /**
     * @var LinkManagementInterface $productLinkManagement
     */
    private LinkManagementInterface $linkManagement;

    /**
     * @param LinkManagementInterface $linkManagement
     */
    public function __construct(
        LinkManagementInterface $linkManagement
    ) {
        $this->linkManagement = $linkManagement;
    }

    /**
     * @param $sku
     * @return string[]
     */
    public function execute($sku) : array
    {
        /**
         * @var LinkInterface[] $items
         */
        $items = $this->linkManagement->getChildren($sku);
        $skus = [];
        foreach($items as $item)
        {
            $skus[] = $item['sku'];
        }
        return $skus;
    }

}
