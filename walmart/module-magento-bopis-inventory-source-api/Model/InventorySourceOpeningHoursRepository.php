<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterfaceFactory;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursSearchResultsInterfaceFactory;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceOpeningHours
    as ResourceInventorySourceOpeningHours;
use Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceOpeningHours\CollectionFactory
    as InventorySourceOpeningHoursCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class InventorySourceOpeningHoursRepository implements InventorySourceOpeningHoursRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var InventorySourceOpeningHoursCollectionFactory
     */
    private InventorySourceOpeningHoursCollectionFactory $inventorySourceOpeningHoursCollectionFactory;

    /**
     * @var InventorySourceOpeningHoursFactory
     */
    private InventorySourceOpeningHoursFactory $inventorySourceOpeningHoursFactory;

    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $extensionAttributesJoinProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private ExtensibleDataObjectConverter $extensibleDataObjectConverter;

    /**
     * @var ResourceInventorySourceOpeningHours
     */
    private ResourceInventorySourceOpeningHours $resource;

    /**
     * @var DataObjectProcessor
     */
    private DataObjectProcessor $dataObjectProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var InventorySourceOpeningHoursInterfaceFactory
     */
    private InventorySourceOpeningHoursInterfaceFactory $dataInventorySourceOpeningHoursFactory;

    /**
     * @var InventorySourceOpeningHoursSearchResultsInterfaceFactory
     */
    private InventorySourceOpeningHoursSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param ResourceInventorySourceOpeningHours                      $resource
     * @param InventorySourceOpeningHoursFactory                       $inventorySourceOpeningHoursFactory
     * @param InventorySourceOpeningHoursInterfaceFactory              $dataInventorySourceOpeningHoursFactory
     * @param InventorySourceOpeningHoursCollectionFactory             $inventorySourceOpeningHoursCollectionFactory
     * @param InventorySourceOpeningHoursSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                                         $dataObjectHelper
     * @param DataObjectProcessor                                      $dataObjectProcessor
     * @param StoreManagerInterface                                    $storeManager
     * @param CollectionProcessorInterface                             $collectionProcessor
     * @param JoinProcessorInterface                                   $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter                            $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder                                    $searchCriteriaBuilder
     */
    public function __construct(
        ResourceInventorySourceOpeningHours $resource,
        InventorySourceOpeningHoursFactory $inventorySourceOpeningHoursFactory,
        InventorySourceOpeningHoursInterfaceFactory $dataInventorySourceOpeningHoursFactory,
        InventorySourceOpeningHoursCollectionFactory $inventorySourceOpeningHoursCollectionFactory,
        InventorySourceOpeningHoursSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resource = $resource;
        $this->inventorySourceOpeningHoursFactory = $inventorySourceOpeningHoursFactory;
        $this->inventorySourceOpeningHoursCollectionFactory = $inventorySourceOpeningHoursCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataInventorySourceOpeningHoursFactory = $dataInventorySourceOpeningHoursFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
    ) {
        /* if (empty($inventorySourceOpeningHours->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $inventorySourceOpeningHours->setStoreId($storeId);
        } */

        $inventorySourceOpeningHoursData = $this->extensibleDataObjectConverter->toNestedArray(
            $inventorySourceOpeningHours,
            [],
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface::class
        );

        $inventorySourceOpeningHoursModel =
            $this->inventorySourceOpeningHoursFactory->create()->setData($inventorySourceOpeningHoursData);

        try {
            $this->resource->save($inventorySourceOpeningHoursModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the inventorySourceOpeningHours: %1',
                    $exception->getMessage()
                )
            );
        }

        return $inventorySourceOpeningHoursModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($inventorySourceOpeningHoursId)
    {
        $inventorySourceOpeningHours = $this->inventorySourceOpeningHoursFactory->create();
        $this->resource->load($inventorySourceOpeningHours, $inventorySourceOpeningHoursId);
        if (!$inventorySourceOpeningHours->getId()) {
            throw new NoSuchEntityException(
                __('inventory_source_opening_hours with id "%1" does not exist.', $inventorySourceOpeningHoursId)
            );
        }

        return $inventorySourceOpeningHours;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->inventorySourceOpeningHoursCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListBySourceCode(
        string $sourceCode
    ) {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface::SOURCE_CODE,
            $sourceCode,
            'eq'
        )->create();

        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterface $inventorySourceOpeningHours
    ) {
        try {
            $this->resource->delete($inventorySourceOpeningHours);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the inventory_source_opening_hours: %1',
                    $e->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllBySourceCode(
        string $sourceCode
    ) {
        foreach ($this->getListBySourceCode($sourceCode)->getItems() as $item) {
            $this->delete($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($inventorySourceOpeningHoursId)
    {
        return $this->delete($this->get($inventorySourceOpeningHoursId));
    }
}
