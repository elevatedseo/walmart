<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Get map of SKU to `available_home_delivery` product value
 */
class GetHomeDeliveryFlagBySkus
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * Get map of SKU to `available_home_delivery` custom product attribute value
     *
     * @param array $skuList
     * @return array
     */
    public function execute(array $skuList): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ProductInterface::SKU, $skuList, 'in')
            ->addFilter('available_home_delivery', 1);
        $searchResult = $this->productRepository->getList($searchCriteria->create());

        $productsToValueMap = [];
        foreach ($searchResult->getItems() as $product) {
            $productsToValueMap[$product->getSku()] = 1;
        }

        if (count($productsToValueMap) < count($skuList)) {
            foreach ($skuList as $sku) {
                if (!isset($productsToValueMap[$sku])) {
                    $productsToValueMap[$sku] = 0;
                }
            }
        }

        return $productsToValueMap;
    }
}
