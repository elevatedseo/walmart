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
use Walmart\BopisLocationCheckIn\Model\CarColorFactory;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarColor as CarColorResourceModel;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarColor\CollectionFactory;
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorSearchResultsInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorSearchResultsInterfaceFactory;

class CarColorRepository implements CarColorRepositoryInterface
{
    /**
     * @var CarColorFactory
     */
    private CarColorFactory $carColorFactory;

    /**
     * @var CarColorResourceModel
     */
    private CarColorResourceModel $carColorResourceModel;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $carColorCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var CarColorSearchResultsInterfaceFactory
     */
    private CarColorSearchResultsInterfaceFactory $carColorSearchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @param CarColorFactory $carColorFactory
     * @param CarColorResourceModel $carColorResourceModel
     * @param CollectionFactory $carColorCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CarColorSearchResultsInterfaceFactory $carColorSearchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CarColorFactory $carColorFactory,
        CarColorResourceModel $carColorResourceModel,
        CollectionFactory $carColorCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CarColorSearchResultsInterfaceFactory $carColorSearchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->carColorFactory = $carColorFactory;
        $this->carColorResourceModel = $carColorResourceModel;
        $this->carColorCollectionFactory = $carColorCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->carColorSearchResultsFactory = $carColorSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritDoc
     */
    public function create(): CarColorInterface
    {
        return $this->carColorFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function save(CarColorInterface $carColor): CarColorInterface
    {
        try {
            $this->carColorResourceModel->save($carColor);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save car color %1', $carColor->getCarColorId()));
        }

        return $carColor;
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): CarColorInterface
    {
        $carColor = $this->create();
        $this->carColorResourceModel->load($carColor, $id);

        if (!$carColor->getCarColorId()) {
            throw new NoSuchEntityException(__('Car Color with specified ID "%1" not found.', $id));
        }

        return $carColor;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CarColorSearchResultsInterface
    {
        $collection = $this->carColorCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $sortOrder = $this->sortOrderBuilder
                ->setField(CarColorInterface::VALUE)
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $searchCriteria->setSortOrders([$sortOrder]);
        }

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->carColorSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(CarColorInterface $carColor): void
    {
        try {
            $this->carColorResourceModel->delete($carColor);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove car color with ID%',
                    $carColor->getCarColorId()
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
            $carColor = $this->get($id);
            $this->delete($carColor);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove car color'));
        }
    }
}
