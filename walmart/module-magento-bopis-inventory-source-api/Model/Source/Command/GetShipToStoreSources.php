<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\Source\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface;
use Walmart\BopisInventorySourceApi\Api\GetShipToStoreSourcesInterface;

/**
 * Get all Inventory Sources with "Ship to Store" flag enabled
 */
class GetShipToStoreSources implements GetShipToStoreSourcesInterface
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
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $sourceCodes): SourceSearchResultsInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(PickupLocationInterface::ALLOW_SHIP_TO_STORE, 1)
            ->addFilter(SourceInterface::SOURCE_CODE, $sourceCodes, 'in');

        return $this->sourceRepository->getList($searchCriteria->create());
    }
}
