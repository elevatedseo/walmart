<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterfaceFactory;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueSearchResultsInterfaceFactory;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOperationQueueApi\Model\ResourceModel\BopisQueue as ResourceBopisQueue;
use Walmart\BopisOperationQueueApi\Model\ResourceModel\BopisQueue\CollectionFactory as BopisQueueCollectionFactory;

class BopisQueueRepository implements BopisQueueRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var BopisQueueFactory
     */
    private BopisQueueFactory $bopisQueueFactory;

    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $extensionAttributesJoinProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private ExtensibleDataObjectConverter $extensibleDataObjectConverter;

    /**
     * @var BopisQueueCollectionFactory
     */
    private BopisQueueCollectionFactory $bopisQueueCollectionFactory;

    /**
     * @var ResourceBopisQueue
     */
    private ResourceBopisQueue $resource;

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
     * @var BopisQueueSearchResultsInterfaceFactory
     */
    private BopisQueueSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var BopisQueueInterfaceFactory
     */
    private BopisQueueInterfaceFactory $dataBopisQueueFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * BopisQueueRepository constructor.
     *
     * @param ResourceBopisQueue                      $resource
     * @param BopisQueueFactory                       $bopisQueueFactory
     * @param BopisQueueInterfaceFactory              $dataBopisQueueFactory
     * @param BopisQueueCollectionFactory             $bopisQueueCollectionFactory
     * @param BopisQueueSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                        $dataObjectHelper
     * @param DataObjectProcessor                     $dataObjectProcessor
     * @param StoreManagerInterface                   $storeManager
     * @param CollectionProcessorInterface            $collectionProcessor
     * @param JoinProcessorInterface                  $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter           $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder                   $searchCriteriaBuilder
     */
    public function __construct(
        ResourceBopisQueue $resource,
        BopisQueueFactory $bopisQueueFactory,
        BopisQueueInterfaceFactory $dataBopisQueueFactory,
        BopisQueueCollectionFactory $bopisQueueCollectionFactory,
        BopisQueueSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resource = $resource;
        $this->bopisQueueFactory = $bopisQueueFactory;
        $this->bopisQueueCollectionFactory = $bopisQueueCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBopisQueueFactory = $dataBopisQueueFactory;
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
        \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
    ) {
        /* if (empty($bopisQueue->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $bopisQueue->setStoreId($storeId);
        } */

        $bopisQueueData = $this->extensibleDataObjectConverter->toNestedArray(
            $bopisQueue,
            [],
            \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface::class
        );

        $bopisQueueModel = $this->bopisQueueFactory->create()->setData($bopisQueueData);

        try {
            $this->resource->save($bopisQueueModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the bopisQueue: %1',
                    $exception->getMessage()
                )
            );
        }

        return $bopisQueueModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($bopisQueueId)
    {
        $bopisQueue = $this->bopisQueueFactory->create();
        $this->resource->load($bopisQueue, $bopisQueueId);
        if (!$bopisQueue->getId()) {
            throw new NoSuchEntityException(__('bopis_queue with id "%1" does not exist.', $bopisQueueId));
        }

        return $bopisQueue;
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrderId($orderId, ?string $operationType = null)
    {
        $this->searchCriteriaBuilder
            ->addFilter(BopisQueueInterface::ENTITY_TYPE, EntityType::ENTITY_TYPE_ORDER)
            ->addFilter(BopisQueueInterface::ENTITY_ID, $orderId);

        if ($operationType) {
            $this->searchCriteriaBuilder->addFilter(BopisQueueInterface::OPERATION_TYPE, $operationType);
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $queueRepositoryItems = $this->getList($searchCriteria)->getItems();

        foreach ($queueRepositoryItems as $item) {
            return $item;
        }

        throw new NoSuchEntityException(__('bopis_queue with order_id "%1" does not exist.', $orderId));
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->bopisQueueCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface::class
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
    public function delete(
        \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
    ) {
        try {
            $this->resource->delete($bopisQueue);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the bopis_queue: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($bopisQueueId)
    {
        return $this->delete($this->get($bopisQueueId));
    }
}
