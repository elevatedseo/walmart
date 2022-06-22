<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;
use Walmart\BopisInventoryCatalogApi\Api\SkuListProviderInterface;
use Walmart\BopisInventoryCatalogApi\Exception\SkuIsNotAvailableInStockException;

/**
 * This class provides validation for required and unrequired conditions chain
 * @inheriDoc
 */
class AreProductsAvailableConditionChain implements AreProductsAvailableInterface
{
    /**
     * Required conditions return FALSE if at least one
     * condition is NOT satisfied
     *
     * @var AreProductsAvailableInterface[]
     */
    private array $requiredConditions;

    /**
     * Unrequired conditions return TRUE if at least one
     * condition is satisfied
     *
     * @var AreProductsAvailableInterface[]
     */
    private array $unrequiredConditions;

    /**
     * @var SkuListProviderInterface[]
     */
    private array $skuListProviders;

    /**
     * @var ProductRepositoryInterface $productRepository
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param array $conditions
     * @throws LocalizedException
     */
    public function __construct(
        array $skuListProviders,
        array $conditions,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->skuListProviders = $skuListProviders;

        $this->setConditions($conditions);
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheirtDoc
     */
    public function execute(array $skus, array $sources): bool
    {

        $skusFilter = $this->searchCriteriaBuilder->addFilter('sku', $skus, 'in')->create();
        $products = $this->productRepository->getList($skusFilter)->getItems();

        foreach($products as $product)
        {
            /* @var \Magento\Catalog\Model\Product $product  */
            $type = $product->getTypeId();

            if($product->isVirtual())
                continue;

            $subSkus = $this->skuListProviders[$type]->execute($product->getSku());

            try {
                // if any of required condition is not satisfied - product is not available
                foreach ($this->requiredConditions as $requiredCondition) {
                    if ($requiredCondition->execute($subSkus, $sources) === false) {
                        return false;
                    }
                }

                // if any of unrequired condition is satisfied - product is available
                foreach ($this->unrequiredConditions as $unrequiredCondition) {
                    if ($unrequiredCondition->execute($subSkus, $sources) === true) {
                        break;
                    }
                }
            } catch (SkuIsNotAvailableInStockException $e) {
                return false;
            }

        }

        return true;


    }

    /**
     * Split and Sort conditions provided from DI
     *
     * @param array $conditions
     * @throws LocalizedException
     */
    private function setConditions(array $conditions)
    {
        $this->validateConditions($conditions);

        $unrequiredConditions = array_filter(
            $conditions,
            function ($item) {
                return !isset($item['required']);
            }
        );

        $this->unrequiredConditions = array_column($this->sortConditions($unrequiredConditions), 'object');

        $requiredConditions = array_filter(
            $conditions,
            function ($item) {
                return isset($item['required']) && (bool) $item['required'];
            }
        );
        $this->requiredConditions = array_column($this->sortConditions($requiredConditions), 'object');
    }

    /**
     * Conditions validation
     *
     * @param array $conditions
     * @throws LocalizedException
     */
    private function validateConditions(array $conditions): void
    {
        foreach ($conditions as $condition) {
            if (empty($condition['object'])) {
                throw new LocalizedException(__('Parameter "object" must be present.'));
            }

            if (!$condition['object'] instanceof AreProductsAvailableInterface) {
                throw new LocalizedException(
                    __('Condition has to implement ' . AreProductsAvailableInterface::class . ')')
                );
            }

            if (empty($condition['required']) && empty($condition['sort_order'])) {
                throw new LocalizedException(__('Parameter "sort_order" must be present for unrequired conditions.'));
            }

            if (empty($condition['sort_order'])) {
                throw new LocalizedException(__('Parameter "sort_order" must be present for every condition.'));
            }
        }
    }

    /**
     * It's important to use sort to increase performance
     * and set more prioritized conditions
     *
     * @param array $conditions
     * @return array
     */
    private function sortConditions(array $conditions): array
    {
        usort($conditions, function (array $conditionLeft, array $conditionRight) {
            if ($conditionLeft['sort_order'] == $conditionRight['sort_order']) {
                return 0;
            }
            return ($conditionLeft['sort_order'] < $conditionRight['sort_order']) ? -1 : 1;
        });
        return $conditions;
    }
}
