<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionSearchResultsInterface;

/**
 * Interface for Associate Session Repository
 *
 * @api
 */
interface AssociateSessionRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return AssociateSessionInterface
     */
    public function create();

    /**
     * Save associate session
     *
     * @param AssociateSessionInterface $associateSession
     *
     * @return AssociateSessionInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(AssociateSessionInterface $associateSession): AssociateSessionInterface;

    /**
     * Get associate session by id
     *
     * @param int $id
     *
     * @return AssociateSessionInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateSessionInterface;

    /**
     * Get associate session by user id
     *
     * @param int $userId
     *
     * @return AssociateSessionInterface
     * @throws NoSuchEntityException
     */
    public function getByUserId(int $userId): AssociateSessionInterface;

    /**
     * Get associate session by token
     *
     * @param string $token
     *
     * @return AssociateSessionInterface
     * @throws NoSuchEntityException
     */
    public function getByToken(string $token): AssociateSessionInterface;

    /**
     * Retrieve associate sessions matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateSessionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateSessionSearchResultsInterface;

    /**
     * Delete associate session
     *
     * @param AssociateSessionInterface $associateSession
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(AssociateSessionInterface $associateSession): void;

    /**
     * Delete associate session by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;

    /**
     * Delete associate session by user id
     *
     * @param int $userId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteByUserId(int $userId): void;
}
