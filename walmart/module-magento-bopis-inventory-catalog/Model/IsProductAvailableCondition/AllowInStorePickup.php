<?php

namespace Walmart\BopisInventoryCatalog\Model\IsProductAvailableCondition;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;

class AllowInStorePickup implements AreProductsAvailableInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SearchCriteriaBuilder      $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository)
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string[] $skus
     * @param string[] $sources
     *
     * @return bool
     */
    public function execute(array $skus, array $sources): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('available_store_pickup', 1)
            ->addFilter('sku', $skus, 'in')
            ->create();
        $searchResult = $this->productRepository->getList($searchCriteria);

        return $searchResult->getTotalCount() > 0;
    }
}
