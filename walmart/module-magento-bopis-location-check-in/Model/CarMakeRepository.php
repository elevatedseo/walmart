<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisLocationCheckIn\Model\CarMakeFactory;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarMake as CarMakeResourceModel;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarMake\CollectionFactory;
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeSearchResultsInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeSearchResultsInterfaceFactory;

class CarMakeRepository implements CarMakeRepositoryInterface
{
    /**
     * @var \Walmart\BopisLocationCheckIn\Model\CarMakeFactory
     */
    private CarMakeFactory $carMakeFactory;

    /**
     * @var CarMakeResourceModel
     */
    private CarMakeResourceModel $carMakeResourceModel;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $carMakeCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var CarMakeSearchResultsInterfaceFactory
     */
    private CarMakeSearchResultsInterfaceFactory $carMakeSearchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @param \Walmart\BopisLocationCheckIn\Model\CarMakeFactory $carMakeFactory
     * @param CarMakeResourceModel $carMakeResourceModel
     * @param CollectionFactory $carMakeCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CarMakeSearchResultsInterfaceFactory $carMakeSearchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CarMakeFactory $carMakeFactory,
        CarMakeResourceModel $carMakeResourceModel,
        CollectionFactory $carMakeCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CarMakeSearchResultsInterfaceFactory $carMakeSearchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->carMakeFactory = $carMakeFactory;
        $this->carMakeResourceModel = $carMakeResourceModel;
        $this->carMakeCollectionFactory = $carMakeCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->carMakeSearchResultsFactory = $carMakeSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritDoc
     */
    public function create(): CarMakeInterface
    {
        return $this->carMakeFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function save(CarMakeInterface $carmake): CarMakeInterface
    {
        try {
            $this->carMakeResourceModel->save($carmake);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save car make %1', $carmake->getCarMakeId()));
        }

        return $carmake;
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): CarMakeInterface
    {
        $carMake = $this->create();
        $this->carMakeResourceModel->load($carMake, $id);

        if (!$carMake->getCarMakeId()) {
            throw new NoSuchEntityException(__('Car Make with specified ID "%1" not found.', $id));
        }

        return $carMake;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CarMakeSearchResultsInterface
    {
        $collection = $this->carMakeCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $sortOrder = $this->sortOrderBuilder
                ->setField(CarMakeInterface::VALUE)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $searchCriteria->setSortOrders([$sortOrder]);
        }

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->carMakeSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(CarMakeInterface $carmake): void
    {
        try {
            $this->carMakeResourceModel->delete($carmake);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove car make with ID%',
                    $carmake->getCarMakeId()
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): void
    {
        try {
            $carMake = $this->get($id);
            $this->delete($carMake);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove car make'));
        }
    }
}
