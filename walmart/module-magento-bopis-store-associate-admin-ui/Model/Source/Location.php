<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\InventoryApi\Api\Data\SourceInterface as InventorySourceInterface;

class Location extends AbstractSource implements SourceInterface, OptionSourceInterface
{
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
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array[]
     */
    public function getAllOptions(): array
    {
        $options = [];

        $this->searchCriteriaBuilder
            ->addFilter(InventorySourceInterface::ENABLED, 1)
            ->addFilter(PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE, 1);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $inventorySources = $this->sourceRepository->getList($searchCriteria);

        foreach ($inventorySources->getItems() as $source) {
            $options[] = ['value' => $source->getSourceCode(), 'label' => $source->getName()];
        }

        return $options;
    }
}
