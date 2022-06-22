<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CheckIn\CollectionFactory;
use Walmart\BopisLocationCheckInApi\Api\CheckInRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInSearchResultsInterface;
use Walmart\BopisLocationCheckIn\Model\CheckInFactory;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CheckIn as CheckInResourceModel;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInSearchResultsInterfaceFactory;

class CheckInRepository implements CheckInRepositoryInterface
{
    /**
     * @var \Walmart\BopisLocationCheckIn\Model\CheckInFactory
     */
    private CheckInFactory $checkInFactory;

    /**
     * @var CheckInResourceModel
     */
    private CheckInResourceModel $checkInResourceModel;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $checkInCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var CheckInSearchResultsInterfaceFactory
     */
    private CheckInSearchResultsInterfaceFactory $checkInSearchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param \Walmart\BopisLocationCheckIn\Model\CheckInFactory $checkInFactory
     * @param CheckInResourceModel $checkInResourceModel
     * @param CollectionFactory $checkInCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CheckInSearchResultsInterfaceFactory $checkInSearchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CheckInFactory $checkInFactory,
        CheckInResourceModel $checkInResourceModel,
        CollectionFactory $checkInCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CheckInSearchResultsInterfaceFactory $checkInSearchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->checkInFactory = $checkInFactory;
        $this->checkInResourceModel = $checkInResourceModel;
        $this->checkInCollectionFactory = $checkInCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->checkInSearchResultsFactory = $checkInSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function create(): CheckInInterface
    {
        return $this->checkInFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function save(CheckInInterface $checkIn): CheckInInterface
    {
        try {
            $this->checkInResourceModel->save($checkIn);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save check-in %1', $checkIn->getCheckInId()));
        }

        return $checkIn;
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): CheckInInterface
    {
        $checkIn = $this->create();
        $this->checkInResourceModel->load($checkIn, $id);

        if (!$checkIn->getCheckInId()) {
            throw new NoSuchEntityException(__('Check-In with specified ID "%1" not found.', $id));
        }

        return $checkIn;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId(int $orderId): CheckInInterface
    {
        $checkIn = $this->create();
        $this->checkInResourceModel->load($checkIn, $orderId, CheckInInterface::ORDER_ID);

        if (!$checkIn->getCheckInId()) {
            throw new NoSuchEntityException(__('Check-In with specified order ID "%1" not found.', $orderId));
        }

        return $checkIn;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CheckInSearchResultsInterface
    {
        $collection = $this->checkInCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->checkInSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(CheckInInterface $checkIn): void
    {
        try {
            $this->checkInResourceModel->delete($checkIn);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove check-in with ID%',
                    $checkIn->getCheckInId()
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
            $checkIn = $this->get($id);
            $this->delete($checkIn);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove check-in'));
        }
    }
}
