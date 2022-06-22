<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsSearchResultsInterface;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsSearchResultsInterfaceFactory;
use Walmart\BopisOrderFaasSync\Api\OrderLineItemsRepositoryInterface;
use Walmart\BopisOrderFaasSync\Model\OrderLineItems;
use Walmart\BopisOrderFaasSync\Model\OrderLineItemsFactory;
use Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems as OrderLineItemsResourceModel;
use Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems\CollectionFactory;
use Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems\Collection;

/**
 * Repository for Order Line Items
 */
class OrderLineItemsRepository implements OrderLineItemsRepositoryInterface
{
    /**
     * @var OrderLineItemsResourceModel
     */
    private OrderLineItemsResourceModel $orderLineItemsResourceModel;

    /**
     * @var OrderLineItemsFactory
     */
    private OrderLineItemsFactory $orderLineItemsFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $orderLineItemsCollectionFactory;

    /**
     * @var OrderLineItemsSearchResultsInterfaceFactory
     */
    private OrderLineItemsSearchResultsInterfaceFactory $orderLineItemsSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OrderLineItemsResourceModel $orderLineItemsResourceModel
     * @param OrderLineItemsFactory $orderLineItemsFactory
     * @param CollectionFactory $orderLineItemsCollectionFactory
     * @param OrderLineItemsSearchResultsInterfaceFactory $orderLineItemsSearchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderLineItemsResourceModel $orderLineItemsResourceModel,
        OrderLineItemsFactory $orderLineItemsFactory,
        CollectionFactory $orderLineItemsCollectionFactory,
        OrderLineItemsSearchResultsInterfaceFactory $orderLineItemsSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderLineItemsResourceModel = $orderLineItemsResourceModel;
        $this->orderLineItemsFactory = $orderLineItemsFactory;
        $this->orderLineItemsCollectionFactory = $orderLineItemsCollectionFactory;
        $this->orderLineItemsSearchResultsFactory = $orderLineItemsSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return OrderLineItemsInterface
     */
    public function create(): OrderLineItemsInterface
    {
        return $this->orderLineItemsFactory->create();
    }

    /**
     * Save Order Line Items data
     *
     * @param OrderLineItemsInterface $orderLineItems
     *
     * @return OrderLineItemsInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(OrderLineItemsInterface $orderLineItems): OrderLineItemsInterface
    {
        try {
            /** @var OrderLineItems $orderLineItems */
            $this->orderLineItemsResourceModel->save($orderLineItems);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save order line item %1', $orderLineItems->getId())
            );
        }

        return $orderLineItems;
    }

    /**
     * Get Order Line Items by ID
     *
     * @param int $id
     *
     * @return OrderLineItemsInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderLineItemsInterface
    {
        /** @var OrderLineItems $orderLineItems */
        $orderLineItems = $this->create();
        $this->orderLineItemsResourceModel->load($orderLineItems, $id);

        if (!$orderLineItems->getId()) {
            throw new NoSuchEntityException(__('Order line item with specified ID "%1" not found.', $id));
        }

        return $orderLineItems;
    }

    /**
     * Delete Order Line Items
     *
     * @param OrderLineItemsInterface $orderLineItems
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(OrderLineItemsInterface $orderLineItems): void
    {
        try {
            /** @var OrderLineItems $orderLineItems */
            $this->orderLineItemsResourceModel->delete($orderLineItems);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove order line item with ID%',
                    $orderLineItems->getId()
                )
            );
        }
    }

    /**
     * Delete order items line by ID
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
            $orderLineItems = $this->get($id);
            $this->delete($orderLineItems);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove order items line'));
        }
    }

    /**
     * Get order items line matching the specified criteria list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderLineItemsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): OrderLineItemsSearchResultsInterface
    {
        $collection = $this->orderLineItemsCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->orderLineItemsSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
