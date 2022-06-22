<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesSearchResultsInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesSearchResultsInterfaceFactory;
use Walmart\BopisOrderUpdateApi\Api\OrderExtensionAttributesRepositoryInterface;
use Walmart\BopisOrderUpdate\Model\OrderExtensionAttributes;
use Walmart\BopisOrderUpdate\Model\OrderExtensionAttributesFactory;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes as OrderExtensionAttributesResourceModel;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes\CollectionFactory;

/**
 * Repository for Order Extension Attributes
 */
class OrderExtensionAttributesRepository implements OrderExtensionAttributesRepositoryInterface
{
    /**
     * @var OrderExtensionAttributesResourceModel
     */
    private OrderExtensionAttributesResourceModel $orderExtensionAttributesResourceModel;

    /**
     * @var OrderExtensionAttributesFactory
     */
    private OrderExtensionAttributesFactory $orderExtensionAttributesFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $orderExtensionAttributesCollectionFactory;

    /**
     * @var OrderExtensionAttributesSearchResultsInterfaceFactory
     */
    private OrderExtensionAttributesSearchResultsInterfaceFactory $orderExtensionAttributesSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OrderExtensionAttributesResourceModel $orderExtensionAttributesResourceModel
     * @param OrderExtensionAttributesFactory $orderExtensionAttributesFactory
     * @param CollectionFactory $orderExtensionAttributesCollectionFactory
     * @param OrderExtensionAttributesSearchResultsInterfaceFactory $orderExtensionAttributesSearchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderExtensionAttributesResourceModel $orderExtensionAttributesResourceModel,
        OrderExtensionAttributesFactory $orderExtensionAttributesFactory,
        CollectionFactory $orderExtensionAttributesCollectionFactory,
        OrderExtensionAttributesSearchResultsInterfaceFactory $orderExtensionAttributesSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderExtensionAttributesResourceModel = $orderExtensionAttributesResourceModel;
        $this->orderExtensionAttributesFactory = $orderExtensionAttributesFactory;
        $this->orderExtensionAttributesCollectionFactory = $orderExtensionAttributesCollectionFactory;
        $this->orderExtensionAttributesSearchResultsFactory = $orderExtensionAttributesSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return OrderExtensionAttributesInterface
     */
    public function create(): OrderExtensionAttributesInterface
    {
        return $this->orderExtensionAttributesFactory->create();
    }

    /**
     * Save Order Extension Attributes data
     *
     * @param OrderExtensionAttributesInterface $orderExtensionAttributes
     *
     * @return OrderExtensionAttributesInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(OrderExtensionAttributesInterface $orderExtensionAttributes): OrderExtensionAttributesInterface
    {
        try {
            /** @var OrderExtensionAttributes $orderExtensionAttributes */
            $this->orderExtensionAttributesResourceModel->save($orderExtensionAttributes);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save order extension attributes %1', $orderExtensionAttributes->getEntityId())
            );
        }

        return $orderExtensionAttributes;
    }

    /**
     * Get Order Extension Attributes by ID
     *
     * @param int $id
     *
     * @return OrderExtensionAttributesInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderExtensionAttributesInterface
    {
        /** @var OrderExtensionAttributes $orderExtensionAttributes */
        $orderExtensionAttributes = $this->create();
        $this->orderExtensionAttributesResourceModel->load($orderExtensionAttributes, $id);

        if (!$orderExtensionAttributes->getEntityId()) {
            throw new NoSuchEntityException(__('Order extension attributes with ID "%1" not found.', $id));
        }

        return $orderExtensionAttributes;
    }

    /**
     * Get Order Extension Attributes by Order ID
     *
     * @param int $orderId
     *
     * @return OrderExtensionAttributesInterface
     */
    public function getByOrderId(int $orderId): OrderExtensionAttributesInterface
    {
        /** @var OrderExtensionAttributes $orderExtensionAttributes */
        $orderExtensionAttributes = $this->create();
        $this->orderExtensionAttributesResourceModel->load(
            $orderExtensionAttributes,
            $orderId,
            OrderExtensionAttributesInterface::ORDER_ID);

        if (!$orderExtensionAttributes->getEntityId()) {
            return $orderExtensionAttributes;
        }

        return $orderExtensionAttributes;
    }

    /**
     * Delete Order Extension Attributes
     *
     * @param OrderExtensionAttributesInterface $orderExtensionAttributes
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(OrderExtensionAttributesInterface $orderExtensionAttributes): void
    {
        try {
            /** @var OrderExtensionAttributes $orderExtensionAttributes */
            $this->orderExtensionAttributesResourceModel->delete($orderExtensionAttributes);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove order extension attributes with ID%',
                    $orderExtensionAttributes->getEntityId()
                )
            );
        }
    }

    /**
     * Delete Order Extension Attributes by ID
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): void
    {
        try {
            $orderExtensionAttributes = $this->get($id);
            $this->delete($orderExtensionAttributes);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove order extension attributes'));
        }
    }

    /**
     * Get Order Extension Attributes list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderExtensionAttributesSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): OrderExtensionAttributesSearchResultsInterface
    {
        $collection = $this->orderExtensionAttributesCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->orderExtensionAttributesSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
