<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigSearchResultsInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigSearchResultsInterfaceFactory;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateTfa\Model\AssociateTfaConfig;
use Walmart\BopisStoreAssociateTfa\Model\AssociateTfaConfigFactory;
use Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig as AssociateTfaConfigResourceModel;
use Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig\CollectionFactory;
use Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig\Collection;

/**
 * Repository for Associate Tfa Config
 */
class AssociateTfaConfigRepository implements AssociateTfaConfigRepositoryInterface
{
    /**
     * @var AssociateTfaConfigResourceModel
     */
    private AssociateTfaConfigResourceModel $associateTfaConfigResourceModel;

    /**
     * @var AssociateTfaConfigFactory
     */
    private AssociateTfaConfigFactory $associateTfaConfigFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $associateTfaConfigCollectionFactory;

    /**
     * @var AssociateTfaConfigSearchResultsInterfaceFactory
     */
    private AssociateTfaConfigSearchResultsInterfaceFactory $associateTfaConfigSearchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param AssociateTfaConfigResourceModel                 $associateTfaConfigResourceModel
     * @param AssociateTfaConfigFactory                       $associateTfaConfigFactory
     * @param CollectionFactory                               $associateTfaConfigCollectionFactory
     * @param AssociateTfaConfigSearchResultsInterfaceFactory $associateTfaConfigSearchResultsFactory
     * @param CollectionProcessorInterface                    $collectionProcessor
     * @param SearchCriteriaBuilder                           $searchCriteriaBuilder
     */
    public function __construct(
        AssociateTfaConfigResourceModel $associateTfaConfigResourceModel,
        AssociateTfaConfigFactory $associateTfaConfigFactory,
        CollectionFactory $associateTfaConfigCollectionFactory,
        AssociateTfaConfigSearchResultsInterfaceFactory $associateTfaConfigSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->associateTfaConfigResourceModel = $associateTfaConfigResourceModel;
        $this->associateTfaConfigFactory = $associateTfaConfigFactory;
        $this->associateTfaConfigCollectionFactory = $associateTfaConfigCollectionFactory;
        $this->associateTfaConfigSearchResultsFactory = $associateTfaConfigSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize an empty model
     *
     * @return AssociateTfaConfigInterface
     */
    public function create(): AssociateTfaConfigInterface
    {
        return $this->associateTfaConfigFactory->create();
    }

    /**
     * Save Associate Tfa Config data
     *
     * @param AssociateTfaConfigInterface $associateTfaConfig
     *
     * @return AssociateTfaConfigInterface
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function save(AssociateTfaConfigInterface $associateTfaConfig): AssociateTfaConfigInterface
    {
        try {
            $this->associateTfaConfigResourceModel->save($associateTfaConfig);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save associate tfa config %1', $associateTfaConfig->getConfigId())
            );
        }

        return $associateTfaConfig;
    }

    /**
     * Get Associate Tfa Config by ID
     *
     * @param int $id
     *
     * @return AssociateTfaConfigInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateTfaConfigInterface
    {
        /** @var AssociateTfaConfig $associateTfaConfig */
        $associateTfaConfig = $this->create();
        $this->associateTfaConfigResourceModel->load($associateTfaConfig, $id);

        if (!$associateTfaConfig->getConfigId()) {
            throw new NoSuchEntityException(__('Associate tfa config with specified ID "%1" not found.', $id));
        }

        return $associateTfaConfig;
    }

    /**
     * Get Associate Tfa Config by user ID
     *
     * @param int $userId
     *
     * @return AssociateTfaConfigInterface
     */
    public function getByUserId(int $userId): AssociateTfaConfigInterface
    {
        /** @var AssociateTfaConfig $associateTfaConfig */
        $associateTfaConfig = $this->create();
        $this->associateTfaConfigResourceModel->load(
            $associateTfaConfig,
            $userId,
            AssociateTfaConfigInterface::USER_ID
        );

        return $associateTfaConfig;
    }

    /**
     * Delete Associate Tfa Config
     *
     * @param AssociateTfaConfigInterface $associateTfaConfig
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(AssociateTfaConfigInterface $associateTfaConfig): void
    {
        try {
            $this->associateTfaConfigResourceModel->delete($associateTfaConfig);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove associate tfa config with ID%',
                    $associateTfaConfig->getConfigId()
                )
            );
        }
    }

    /**
     * Delete Associate Tfa Config by ID
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
            $associateTfaConfig = $this->get($id);
            $this->delete($associateTfaConfig);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate tfa config'));
        }
    }

    /**
     * Get Associate Tfa Config list
     *
     * @param  SearchCriteriaInterface|null $searchCriteria
     * @return AssociateTfaConfigSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateTfaConfigSearchResultsInterface
    {
        $collection = $this->associateTfaConfigCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $searchResults = $this->associateTfaConfigSearchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete Associate Tfa Config by user ID
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
            $associateTfaConfig = $this->create();
            $this->associateTfaConfigResourceModel->load(
                $associateTfaConfig,
                $userId,
                AssociateTfaConfigInterface::USER_ID
            );
            $this->delete($associateTfaConfig);
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__('Unable to remove associate tfa config'));
        }
    }
}
