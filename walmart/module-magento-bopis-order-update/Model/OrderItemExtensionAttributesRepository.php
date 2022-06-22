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
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesSearchResultsInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesSearchResultsInterfaceFactory;
use Walmart\BopisOrderUpdateApi\Api\OrderItemExtensionAttributesRepositoryInterface;
use Walmart\BopisOrderUpdate\Model\OrderItemExtensionAttributes;
use Walmart\BopisOrderUpdate\Model\OrderItemExtensionAttributesFactory;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderItemExtensionAttributes as OrderIExtensionAttributesResourceModel;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderItemExtensionAttributes\CollectionFactory;

/**
 * Repository for Order Item Extension Attributes
 */
class OrderItemExtensionAttributesRepository implements OrderItemExtensionAttributesRepositoryInterface
{
    /**
     * @var OrderIExtensionAttributesResourceModel
     */
    private OrderIExtensionAttributesResourceModel $orderItemExtensionAttributesResourceModel;

    /**
     * @var OrderItemExtensionAttributesFactory
     */
    private OrderItemExtensionAttributesFactory $orderItemExtensionAttributesFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $orderItemExtensionAttributesCollectionFactory;

    /**
     * @var OrderItemExtensionAttributesSearchResultsInterfaceFactory
     */
    private OrderItemExtensionAttributesSearchResultsInterfaceFactory $orderItemExtensionAttributesSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OrderIExtensionAttributesResourceModel $orderItemExtensionAttributesResourceModel
     * @param OrderItemExtensionAttributesFactory $orderItemExtensionAttributesFactory
     * @param CollectionFactory $orderItemExtensionAttributesCollectionFactory
     * @param OrderItemExtensionAttributesSearchResultsInterfaceFactory $orderItemExtensionAttributesSearchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderIExtensionAttributesResourceModel $orderItemExtensionAttributesResourceModel,
        OrderItemExtensionAttributesFactory $orderItemExtensionAttributesFactory,
        CollectionFactory $orderItemExtensionAttributesCollectionFactory,
        OrderItemExtensionAttributesSearchResultsInterfaceFactory $orderItemExtensionAttributesSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderItemExtensionAttributesResourceModel = $orderItemExtensionAttributesResourceModel;
        $this->orderItemExtensionAttributesFactory = $orderItemExtensionAttributesFactory;
        $this->orderItemExtensionAttributesCollectionFactory = $orderItemExtensionAttributesCollectionFactory;
        $this->orderItemExtensionAttributesSearchResultsFactory = $orderItemExtensionAttributesSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return OrderItemExtensionAttributesInterface
     */
    public function create(): OrderItemExtensionAttributesInterface
    {
        return $this->orderItemExtensionAttributesFactory->create();
    }

    /**
     * Save Order Item Extension Attributes data
     *
     * @param OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
     *
     * @return OrderItemExtensionAttributesInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(
        OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
    ): OrderItemExtensionAttributesInterface
    {
        try {
            /** @var OrderItemExtensionAttributes $orderItemExtensionAttributes */
            $this->orderItemExtensionAttributesResourceModel->save($orderItemExtensionAttributes);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Unable to save order item extension attributes %1',
                    $orderItemExtensionAttributes->getEntityId()
                )
            );
        }

        return $orderItemExtensionAttributes;
    }

    /**
     * Get Order Item Extension Attributes by ID
     *
     * @param int $id
     *
     * @return OrderItemExtensionAttributesInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderItemExtensionAttributesInterface
    {
        /** @var OrderItemExtensionAttributes $orderItemExtensionAttributes */
        $orderItemExtensionAttributes = $this->create();
        $this->orderItemExtensionAttributesResourceModel->load($orderItemExtensionAttributes, $id);

        if (!$orderItemExtensionAttributes->getEntityId()) {
            throw new NoSuchEntityException(__('Order item extension attributes with ID "%1" not found.', $id));
        }

        return $orderItemExtensionAttributes;
    }

    /**
     * Get Order Item Extension Attributes by Order Item ID
     *
     * @param int $orderId
     *
     * @return OrderItemExtensionAttributesInterface
     */
    public function getByOrderItemId(int $orderId): OrderItemExtensionAttributesInterface
    {
        /** @var OrderItemExtensionAttributes $orderItemExtensionAttributes */
        $orderItemExtensionAttributes = $this->create();
        $this->orderItemExtensionAttributesResourceModel->load(
            $orderItemExtensionAttributes,
            $orderId,
            OrderItemExtensionAttributesInterface::ORDER_ITEM_ID);

        if (!$orderItemExtensionAttributes->getEntityId()) {
            return $orderItemExtensionAttributes;
        }

        return $orderItemExtensionAttributes;
    }

    /**
     * Delete Order Item Extension Attributes
     *
     * @param OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(OrderItemExtensionAttributesInterface $orderItemExtensionAttributes): void
    {
        try {
            /** @var OrderItemExtensionAttributes $orderItemExtensionAttributes */
            $this->orderItemExtensionAttributesResourceModel->delete($orderItemExtensionAttributes);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove order item extension attributes with ID%',
                    $orderItemExtensionAttributes->getEntityId()
                )
            );
        }
    }

    /**
     * Delete Order Item Extension Attributes by ID
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
            $orderItemExtensionAttributes = $this->get($id);
            $this->delete($orderItemExtensionAttributes);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove order item extension attributes'));
        }
    }

    /**
     * Get Order Item Extension Attributes list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderItemExtensionAttributesSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): OrderItemExtensionAttributesSearchResultsInterface
    {
        $collection = $this->orderItemExtensionAttributesCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->orderItemExtensionAttributesSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
