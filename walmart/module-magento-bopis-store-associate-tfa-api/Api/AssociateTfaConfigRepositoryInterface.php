<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigSearchResultsInterface;

/**
 * Interface for Associate Tfa Config Repository
 *
 * @api
 */
interface AssociateTfaConfigRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return AssociateTfaConfigInterface
     */
    public function create();

    /**
     * Save associate tfa config
     *
     * @param AssociateTfaConfigInterface $associateTfaConfig
     *
     * @return AssociateTfaConfigInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(AssociateTfaConfigInterface $associateTfaConfig): AssociateTfaConfigInterface;

    /**
     * Get associate tfa config by id
     *
     * @param int $id
     *
     * @return AssociateTfaConfigInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateTfaConfigInterface;

    /**
     * Get associate tfa config by user id
     *
     * @param int $userId
     *
     * @return AssociateTfaConfigInterface
     * @throws NoSuchEntityException
     */
    public function getByUserId(int $userId): AssociateTfaConfigInterface;

    /**
     * Retrieve associate tfa configs matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return AssociateTfaConfigSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): AssociateTfaConfigSearchResultsInterface;

    /**
     * Delete associate tfa config
     *
     * @param AssociateTfaConfigInterface $associateTfaConfig
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(AssociateTfaConfigInterface $associateTfaConfig): void;

    /**
     * Delete associate tfa config by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;

    /**
     * Delete associate tfa config by user id
     *
     * @param int $userId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteByUserId(int $userId): void;
}
