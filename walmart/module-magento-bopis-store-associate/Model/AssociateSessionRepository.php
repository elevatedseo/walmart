<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionSearchResultsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionSearchResultsInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\AssociateSession;
use Walmart\BopisStoreAssociate\Model\AssociateSessionFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession as AssociateSessionResourceModel;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession\CollectionFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession\Collection;

/**
 * Repository for Associate Session
 */
class AssociateSessionRepository implements AssociateSessionRepositoryInterface
{
    /**
     * @var AssociateSessionResourceModel
     */
    private AssociateSessionResourceModel $associateSessionResourceModel;

    /**
     * @var AssociateSessionFactory
     */
    private AssociateSessionFactory $associateSessionFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $associateSessionCollectionFactory;

    /**
     * @var AssociateSessionSearchResultsInterfaceFactory
     */
    private AssociateSessionSearchResultsInterfaceFactory $associateSessionSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AssociateSessionResourceModel                 $associateSessionResourceModel
     * @param AssociateSessionFactory                       $associateSessionFactory
     * @param CollectionFactory                             $associateSessionCollectionFactory
     * @param AssociateSessionSearchResultsInterfaceFactory $associateSessionSearchResultsFactory
     * @param CollectionProcessorInterface                  $collectionProcessor
     * @param SearchCriteriaBuilder                         $searchCriteriaBuilder
     */
    public function __construct(
        AssociateSessionResourceModel $associateSessionResourceModel,
        AssociateSessionFactory $associateSessionFactory,
        CollectionFactory $associateSessionCollectionFactory,
        AssociateSessionSearchResultsInterfaceFactory $associateSessionSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->associateSessionResourceModel = $associateSessionResourceModel;
        $this->associateSessionFactory = $associateSessionFactory;
        $this->associateSessionCollectionFactory = $associateSessionCollectionFactory;
        $this->associateSessionSearchResultsFactory = $associateSessionSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return AssociateSessionInterface
     */
    public function create(): AssociateSessionInterface
    {
        return $this->associateSessionFactory->create();
    }

    /**
     * Save Associate Session data
     *
     * @param AssociateSessionInterface $associateSession
     *
     * @return AssociateSessionInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(AssociateSessionInterface $associateSession): AssociateSessionInterface
    {
        try {
            $this->associateSessionResourceModel->save($associateSession);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save associate session %1', $associateSession->getSessionId())
            );
        }

        return $associateSession;
    }

    /**
     * Get Associate Session by ID
     *
     * @param int $id
     *
     * @return AssociateSessionInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateSessionInterface
    {
        /** @var AssociateSession $associateSession */
        $associateSession = $this->create();
        $this->associateSessionResourceModel->load($associateSession, $id);

        if (!$associateSession->getSessionId()) {
            throw new NoSuchEntityException(__('Associate session with specified ID "%1" not found.', $id));
        }

        return $associateSession;
    }

    /**
     * Get Associate Session by User ID
     *
     * @param int $userId
     *
     * @return AssociateSessionInterface
     * @throws NoSuchEntityException
     */
    public function getByUserId(int $userId): AssociateSessionInterface
    {
        /** @var AssociateSession $associateSession */
        $associateSession = $this->create();
        $this->associateSessionResourceModel->load($associateSession, $userId, AssociateSessionInterface::USER_ID);

        if (!$associateSession->getSessionId()) {
            throw new NoSuchEntityException(__('Associate session with specified ID "%1" not found.', $userId));
        }

        return $associateSession;
    }

    /**
     * Get Associate Session by token
     *
     * @param string $token
     *
     * @return AssociateSessionInterface
     */
    public function getByToken(string $token): AssociateSessionInterface
    {
        /** @var AssociateSession $associateSession */
        $associateSession = $this->create();
        $this->associateSessionResourceModel->load($associateSession, $token, AssociateSessionInterface::TOKEN);

        return $associateSession;
    }

    /**
     * Delete Associate Session
     *
     * @param AssociateSessionInterface $associateSession
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(AssociateSessionInterface $associateSession): void
    {
        try {
            $this->associateSessionResourceModel->delete($associateSession);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove associate session with ID%',
                    $associateSession->getSessionId()
                )
            );
        }
    }

    /**
     * Delete Associate Session by ID
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
            $associateSession = $this->get($id);
            $this->delete($associateSession);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate session'));
        }
    }

    /**
     * Get Associate Session list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateSessionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateSessionSearchResultsInterface
    {
        $collection = $this->associateSessionCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->associateSessionSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete Associate Session by user ID
     *
     * @param int $userId
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @return void
     */
    public function deleteByUserId(int $userId): void
    {
        try {
            $associateSession = $this->create();
            $this->associateSessionResourceModel->load($associateSession, $userId, AssociateSessionInterface::USER_ID);
            $this->delete($associateSession);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate session'));
        }
    }
}
