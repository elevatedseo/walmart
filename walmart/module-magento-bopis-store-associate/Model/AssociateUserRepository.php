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
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserSearchResultsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserSearchResultsInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\AssociateUser;
use Walmart\BopisStoreAssociate\Model\AssociateUserFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser as AssociateUserResourceModel;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser\CollectionFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser\Collection;

/**
 * Repository for Associate User
 */
class AssociateUserRepository implements AssociateUserRepositoryInterface
{
    /**
     * @var AssociateUserResourceModel
     */
    private AssociateUserResourceModel $associateUserResourceModel;

    /**
     * @var AssociateUserFactory
     */
    private AssociateUserFactory $associateUserFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $associateUserCollectionFactory;

    /**
     * @var AssociateUserSearchResultsInterfaceFactory
     */
    private AssociateUserSearchResultsInterfaceFactory $associateUserSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AssociateUserResourceModel                 $associateUserResourceModel
     * @param AssociateUserFactory                       $associateUserFactory
     * @param CollectionFactory                          $associateUserCollectionFactory
     * @param AssociateUserSearchResultsInterfaceFactory $associateUserSearchResultsFactory
     * @param CollectionProcessorInterface               $collectionProcessor
     * @param SearchCriteriaBuilder                      $searchCriteriaBuilder
     */
    public function __construct(
        AssociateUserResourceModel $associateUserResourceModel,
        AssociateUserFactory $associateUserFactory,
        CollectionFactory $associateUserCollectionFactory,
        AssociateUserSearchResultsInterfaceFactory $associateUserSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->associateUserResourceModel = $associateUserResourceModel;
        $this->associateUserFactory = $associateUserFactory;
        $this->associateUserCollectionFactory = $associateUserCollectionFactory;
        $this->associateUserSearchResultsFactory = $associateUserSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return AssociateUserInterface
     */
    public function create(): AssociateUserInterface
    {
        return $this->associateUserFactory->create();
    }

    /**
     * Save Associate User data
     *
     * @param AssociateUserInterface $associateUser
     *
     * @return AssociateUserInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(AssociateUserInterface $associateUser): AssociateUserInterface
    {
        try {
            $this->associateUserResourceModel->save($associateUser);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save associate user %1', $associateUser->getUserId()));
        }

        return $associateUser;
    }

    /**
     * Get Associate User by ID
     *
     * @param int $id
     *
     * @return AssociateUserInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateUserInterface
    {
        /** @var AssociateUser $associateUser */
        $associateUser = $this->create();
        $this->associateUserResourceModel->load($associateUser, $id);

        if (!$associateUser->getUserId()) {
            throw new NoSuchEntityException(__('Associate user with specified ID "%1" not found.', $id));
        }

        return $associateUser;
    }

    /**
     * Get Associate User by username
     *
     * @param string $username
     *
     * @return AssociateUserInterface
     * @throws NoSuchEntityException
     */
    public function getByUsername(string $username): AssociateUserInterface
    {
        /** @var AssociateUser $associateUser */
        $associateUser = $this->create();
        $this->associateUserResourceModel->load($associateUser, $username, AssociateUserInterface::USERNAME);

        if (!$associateUser->getUserId()) {
            throw new NoSuchEntityException(__('Associate user with specified username "%1" not found.', $username));
        }

        return $associateUser;
    }

    /**
     * Delete Associate User
     *
     * @param AssociateUserInterface $associateUser
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(AssociateUserInterface $associateUser): void
    {
        try {
            $this->associateUserResourceModel->delete($associateUser);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove associate user with ID%',
                    $associateUser->getUserId()
                )
            );
        }
    }

    /**
     * Delete Associate User by ID
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
            $associateUser = $this->get($id);
            $this->delete($associateUser);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate user'));
        }
    }

    /**
     * Get Associate User list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateUserSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateUserSearchResultsInterface
    {
        $collection = $this->associateUserCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->associateUserSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
