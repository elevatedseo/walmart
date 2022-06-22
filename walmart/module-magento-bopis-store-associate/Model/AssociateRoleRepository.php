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
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleSearchResultsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleSearchResultsInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\AssociateRole;
use Walmart\BopisStoreAssociate\Model\AssociateRoleFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole as AssociateRoleResourceModel;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole\CollectionFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole\Collection;

/**
 * Repository for Associate Role
 */
class AssociateRoleRepository implements AssociateRoleRepositoryInterface
{
    /**
     * @var AssociateRoleResourceModel
     */
    private AssociateRoleResourceModel $associateRoleResourceModel;

    /**
     * @var AssociateRoleFactory
     */
    private AssociateRoleFactory $associateRoleFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $associateRoleCollectionFactory;

    /**
     * @var AssociateRoleSearchResultsInterfaceFactory
     */
    private AssociateRoleSearchResultsInterfaceFactory $associateRoleSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AssociateRoleResourceModel                 $associateRoleResourceModel
     * @param AssociateRoleFactory                       $associateRoleFactory
     * @param CollectionFactory                          $associateRoleCollectionFactory
     * @param AssociateRoleSearchResultsInterfaceFactory $associateRoleSearchResultsFactory
     * @param CollectionProcessorInterface               $collectionProcessor
     * @param SearchCriteriaBuilder                      $searchCriteriaBuilder
     */
    public function __construct(
        AssociateRoleResourceModel $associateRoleResourceModel,
        AssociateRoleFactory $associateRoleFactory,
        CollectionFactory $associateRoleCollectionFactory,
        AssociateRoleSearchResultsInterfaceFactory $associateRoleSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->associateRoleResourceModel = $associateRoleResourceModel;
        $this->associateRoleFactory = $associateRoleFactory;
        $this->associateRoleCollectionFactory = $associateRoleCollectionFactory;
        $this->associateRoleSearchResultsFactory = $associateRoleSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return AssociateRoleInterface
     */
    public function create(): AssociateRoleInterface
    {
        return $this->associateRoleFactory->create();
    }

    /**
     * Save Associate Role data
     *
     * @param AssociateRoleInterface $associateRole
     *
     * @return AssociateRoleInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(AssociateRoleInterface $associateRole): AssociateRoleInterface
    {
        try {
            $this->associateRoleResourceModel->save($associateRole);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save associate role %1', $associateRole->getRoleId()));
        }

        return $associateRole;
    }

    /**
     * Get Associate Role by ID
     *
     * @param int $id
     *
     * @return AssociateRoleInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateRoleInterface
    {
        /** @var AssociateRole $associateRole */
        $associateRole = $this->create();
        $this->associateRoleResourceModel->load($associateRole, $id);

        if (!$associateRole->getRoleId()) {
            throw new NoSuchEntityException(__('Associate role with specified ID "%1" not found.', $id));
        }

        return $associateRole;
    }

    /**
     * Delete Associate Role
     *
     * @param AssociateRoleInterface $associateRole
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(AssociateRoleInterface $associateRole): void
    {
        try {
            $this->associateRoleResourceModel->delete($associateRole);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove associate role with ID%',
                    $associateRole->getRoleId()
                )
            );
        }
    }

    /**
     * Delete Associate Role by ID
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
            $associateRole = $this->get($id);
            $this->delete($associateRole);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate role'));
        }
    }

    /**
     * Get Associate Role list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateRoleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateRoleSearchResultsInterface
    {
        $collection = $this->associateRoleCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->associateRoleSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
