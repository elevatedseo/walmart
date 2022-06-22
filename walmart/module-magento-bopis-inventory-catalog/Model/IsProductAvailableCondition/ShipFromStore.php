<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\IsProductAvailableCondition;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface;


/**
 * Another way (the simplest way) to implement condition for product availability validation
 *
 * Validation of Sources with "Ship to Store" flag enabled
 * assigned to any product type
 */
class ShipFromStore implements AreProductsAvailableInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SourceItemRepositoryInterface
     */
    private SourceItemRepositoryInterface $sourceItemRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param SourceItemRepositoryInterface $sourceItemRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        SourceItemRepositoryInterface $sourceItemRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->sourceItemRepository = $sourceItemRepository;
    }

    /**
     * Is any Source with "Ship To Store" flag enabled is assigned to the product
     */
    public function execute(array $skus, array $sources): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceInterface::ENABLED, 1)
            ->addFilter(PickupLocationInterface::USE_AS_SHIPPING_SOURCE, 1);
        $shipToStoreSourceList = $this->sourceRepository->getList($searchCriteria->create());

        $sourceCodes = [];
        foreach ($shipToStoreSourceList->getItems() as $source) {
            $sourceCodes[] = $source->getSourceCode();
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $skus, 'in')
            ->addFilter(SourceItemInterface::SOURCE_CODE, $sourceCodes, 'in')
            ->addFilter(SourceItemInterface::STATUS, SourceItemInterface::STATUS_IN_STOCK);
        $searchResult = $this->sourceItemRepository->getList($searchCriteria->create());

        return (bool) $searchResult->getTotalCount();
    }
}
