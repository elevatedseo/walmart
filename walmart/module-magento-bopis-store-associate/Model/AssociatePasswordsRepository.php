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
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsSearchResultsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsSearchResultsInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\AssociatePasswords;
use Walmart\BopisStoreAssociate\Model\AssociatePasswordsFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords as AssociatePasswordsResourceModel;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords\CollectionFactory;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords\Collection;

/**
 * Repository for Associate Passwords
 */
class AssociatePasswordsRepository implements AssociatePasswordsRepositoryInterface
{
    /**
     * @var AssociatePasswordsResourceModel
     */
    private AssociatePasswordsResourceModel $associatePasswordsResourceModel;

    /**
     * @var AssociatePasswordsFactory
     */
    private AssociatePasswordsFactory $associatePasswordsFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $associatePasswordsCollectionFactory;

    /**
     * @var AssociatePasswordsSearchResultsInterfaceFactory
     */
    private AssociatePasswordsSearchResultsInterfaceFactory $associatePasswordsSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AssociatePasswordsResourceModel                 $associatePasswordsResourceModel
     * @param AssociatePasswordsFactory                       $associatePasswordsFactory
     * @param CollectionFactory                               $associatePasswordsCollectionFactory
     * @param AssociatePasswordsSearchResultsInterfaceFactory $associatePasswordsSearchResultsFactory
     * @param CollectionProcessorInterface                    $collectionProcessor
     * @param SearchCriteriaBuilder                           $searchCriteriaBuilder
     */
    public function __construct(
        AssociatePasswordsResourceModel $associatePasswordsResourceModel,
        AssociatePasswordsFactory $associatePasswordsFactory,
        CollectionFactory $associatePasswordsCollectionFactory,
        AssociatePasswordsSearchResultsInterfaceFactory $associatePasswordsSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->associatePasswordsResourceModel = $associatePasswordsResourceModel;
        $this->associatePasswordsFactory = $associatePasswordsFactory;
        $this->associatePasswordsCollectionFactory = $associatePasswordsCollectionFactory;
        $this->associatePasswordsSearchResultsFactory = $associatePasswordsSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return AssociatePasswordsInterface
     */
    public function create(): AssociatePasswordsInterface
    {
        return $this->associatePasswordsFactory->create();
    }

    /**
     * Save Associate Passwords data
     *
     * @param AssociatePasswordsInterface $associatePasswords
     *
     * @return AssociatePasswordsInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(AssociatePasswordsInterface $associatePasswords): AssociatePasswordsInterface
    {
        try {
            $this->associatePasswordsResourceModel->save($associatePasswords);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save associate password %1', $associatePasswords->getPasswordId())
            );
        }

        return $associatePasswords;
    }

    /**
     * Get Associate Password by ID
     *
     * @param int $id
     *
     * @return AssociatePasswordsInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociatePasswordsInterface
    {
        /** @var AssociatePasswords $associatePassword */
        $associatePassword = $this->create();
        $this->associatePasswordsResourceModel->load($associatePassword, $id);

        if (!$associatePassword->getPasswordId()) {
            throw new NoSuchEntityException(__('Associate password with specified ID "%1" not found.', $id));
        }

        return $associatePassword;
    }

    /**
     * Delete Associate Password
     *
     * @param AssociatePasswordsInterface $associatePassword
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(AssociatePasswordsInterface $associatePassword): void
    {
        try {
            $this->associatePasswordsResourceModel->delete($associatePassword);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove associate password with ID%',
                    $associatePassword->getPasswordId()
                )
            );
        }
    }

    /**
     * Delete Associate Password by ID
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
            $associatePassword = $this->get($id);
            $this->delete($associatePassword);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate password'));
        }
    }

    /**
     * Get Associate Passwords list
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociatePasswordsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociatePasswordsSearchResultsInterface
    {
        $collection = $this->associatePasswordsCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->associatePasswordsSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setSearchCriteria($searchCriteria);

        return $searchResults;
    }
}
