<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Convert\DataObject;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

/**
 * List of values for "selected_inventory_source" customer attribute
 */
class SelectedInventorySource extends Table
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var DataObject
     */
    private DataObject $converter;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /**
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param OptionFactory $attrOptionFactory
     * @param SourceRepositoryInterface $sourceRepository
     * @param DataObject $converter
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        CollectionFactory $attrOptionCollectionFactory,
        OptionFactory $attrOptionFactory,
        SourceRepositoryInterface $sourceRepository,
        DataObject $converter,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->sourceRepository = $sourceRepository;
        $this->converter = $converter;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $searchCriteria = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria->addFilter(SourceInterface::ENABLED, 1);

            $sourceResult = $this->sourceRepository->getList($searchCriteria->create());
            $this->_options = $this->converter->toOptionArray($sourceResult->getItems(), 'source_code', 'name');
        }

        array_unshift($this->_options, ['value' => '', 'label' => __('--Choose one--')]);
        return $this->_options;
    }
}
