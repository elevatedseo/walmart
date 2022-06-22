<?php

namespace Walmart\BopisInventoryCatalog\Model\IsProductAvailableCondition;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface;

class PickupSource implements AreProductsAvailableInterface
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
        SearchCriteriaBuilder          $searchCriteriaBuilder,
        SourceRepositoryInterface      $sourceRepository,
        SourceItemRepositoryInterface  $sourceItemRepository
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->sourceItemRepository = $sourceItemRepository;
    }

    /**
     * Is any sources assigned to product
     * @param string[] $skus
     * @param string[] $sources
     * @return bool
     */
    public function execute(array $skus, array $sources): bool
    {

        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(SourceInterface::ENABLED, 1)
                ->addFilter(PickupLocationInterface::STORE_PICKUP_ENABLED, 1);

            if(!empty($sources))
            {
                $searchCriteria->addFilter(SourceInterface::SOURCE_CODE, $sources, 'in');
            }

            $shipToStoreSourceList = $this->sourceRepository->getList($searchCriteria->create());

            if($shipToStoreSourceList->getTotalCount() == 0)
                return false;

            $filteredSource = [];
            foreach($shipToStoreSourceList->getItems() as $source)
            {
                $filteredSource[] = $source->getSourceCode();
            }

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(SourceItemInterface::SKU, $skus, 'in')
                ->addFilter(SourceItemInterface::SOURCE_CODE, $filteredSource, 'in')
                ->addFilter(SourceItemInterface::STATUS, SourceItemInterface::STATUS_IN_STOCK);
            $sourceItemsList = $this->sourceItemRepository->getList($searchCriteria->create());

            $items = $sourceItemsList->getItems();


            return (bool)$sourceItemsList->getTotalCount();
        }
        catch (NoSuchEntityException $e)
        {
            return false;
        }
    }
}
