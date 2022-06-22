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
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterfaceFactory;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotSearchResultsInterfaceFactory;
use Walmart\BopisInventorySourceApi\Api\InventorySourceParkingSpotRepositoryInterface;
use Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceParkingSpot
    as ResourceInventorySourceParkingSpot;
use Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceParkingSpot\CollectionFactory
    as InventorySourceParkingSpotCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class InventorySourceParkingSpotRepository implements InventorySourceParkingSpotRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $extensionAttributesJoinProcessor;

    /**
     * @var InventorySourceParkingSpotFactory
     */
    private InventorySourceParkingSpotFactory $inventorySourceParkingSpotFactory;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private ExtensibleDataObjectConverter $extensibleDataObjectConverter;

    /**
     * @var ResourceInventorySourceParkingSpot
     */
    private ResourceInventorySourceParkingSpot $resource;

    /**
     * @var InventorySourceParkingSpotCollectionFactory
     */
    private InventorySourceParkingSpotCollectionFactory $inventorySourceParkingSpotCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    private DataObjectProcessor $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var InventorySourceParkingSpotSearchResultsInterfaceFactory
     */
    private InventorySourceParkingSpotSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var InventorySourceParkingSpotInterfaceFactory
     */
    private InventorySourceParkingSpotInterfaceFactory $dataInventorySourceParkingSpotFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param ResourceInventorySourceParkingSpot                      $resource
     * @param InventorySourceParkingSpotFactory                       $inventorySourceParkingSpotFactory
     * @param InventorySourceParkingSpotInterfaceFactory              $dataInventorySourceParkingSpotFactory
     * @param InventorySourceParkingSpotCollectionFactory             $inventorySourceParkingSpotCollectionFactory
     * @param InventorySourceParkingSpotSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                                        $dataObjectHelper
     * @param DataObjectProcessor                                     $dataObjectProcessor
     * @param StoreManagerInterface                                   $storeManager
     * @param CollectionProcessorInterface                            $collectionProcessor
     * @param JoinProcessorInterface                                  $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter                           $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder                                   $searchCriteriaBuilder
     */
    public function __construct(
        ResourceInventorySourceParkingSpot $resource,
        InventorySourceParkingSpotFactory $inventorySourceParkingSpotFactory,
        InventorySourceParkingSpotInterfaceFactory $dataInventorySourceParkingSpotFactory,
        InventorySourceParkingSpotCollectionFactory $inventorySourceParkingSpotCollectionFactory,
        InventorySourceParkingSpotSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resource = $resource;
        $this->inventorySourceParkingSpotFactory = $inventorySourceParkingSpotFactory;
        $this->inventorySourceParkingSpotCollectionFactory = $inventorySourceParkingSpotCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataInventorySourceParkingSpotFactory = $dataInventorySourceParkingSpotFactory;
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
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
    ) {
        /* if (empty($inventorySourceParkingSpot->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $inventorySourceParkingSpot->setStoreId($storeId);
        } */

        $inventorySourceParkingSpotData = $this->extensibleDataObjectConverter->toNestedArray(
            $inventorySourceParkingSpot,
            [],
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface::class
        );

        $inventorySourceParkingSpotModel =
            $this->inventorySourceParkingSpotFactory->create()->setData($inventorySourceParkingSpotData);

        try {
            $this->resource->save($inventorySourceParkingSpotModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the inventorySourceParkingSpot: %1',
                    $exception->getMessage()
                )
            );
        }

        return $inventorySourceParkingSpotModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($inventorySourceParkingSpotId)
    {
        $inventorySourceParkingSpot = $this->inventorySourceParkingSpotFactory->create();
        $this->resource->load($inventorySourceParkingSpot, $inventorySourceParkingSpotId);
        if (!$inventorySourceParkingSpot->getId()) {
            throw new NoSuchEntityException(
                __('inventory_source_parking_spot with id "%1" does not exist.', $inventorySourceParkingSpotId)
            );
        }

        return $inventorySourceParkingSpot;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->inventorySourceParkingSpotCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface::class
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
            \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface::SOURCE_CODE,
            $sourceCode,
            'eq'
        )->create();

        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterface $inventorySourceParkingSpot
    ) {
        try {
            $this->resource->delete($inventorySourceParkingSpot);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the inventory_source_parking_spot: %1',
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
    public function deleteById($inventorySourceParkingSpotId)
    {
        return $this->delete($this->get($inventorySourceParkingSpotId));
    }
}
