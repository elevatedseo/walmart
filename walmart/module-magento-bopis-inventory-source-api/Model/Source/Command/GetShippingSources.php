<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\Source\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\Data\PickupLocationInterface;
use Walmart\BopisInventorySourceApi\Api\GetShippingSourcesInterface;

/**
 * Get all Shipping Sources
 */
class GetShippingSources implements GetShippingSourcesInterface
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
    public function execute(): SourceSearchResultsInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(PickupLocationInterface::USE_AS_SHIPPING_SOURCE, 1);

        return $this->sourceRepository->getList($searchCriteria->create());
    }
}
