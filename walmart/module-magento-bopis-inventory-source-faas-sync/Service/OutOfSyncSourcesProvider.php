<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisInventorySourceFaasSync\Model\AttributeName;

class OutOfSyncSourcesProvider
{
    private const BATCH_SIZE = 200;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                AttributeName::IS_SYNCED,
                0
            )
            ->addFilter('enabled', 1)
            ->addFilter('source_code', 'default', 'neq')
            ->addFilter('is_pickup_location_active', 1)
            ->create();
        $searchCriteria->setPageSize(self::BATCH_SIZE);

        return $this->sourceRepository->getList($searchCriteria)->getItems();
    }
}
