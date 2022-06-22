<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserSearchResultsInterface;

/**
 * Interface for Associate User Repository
 *
 * @api
 */
interface AssociateUserRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return AssociateUserInterface
     */
    public function create();

    /**
     * Save associate user
     *
     * @param AssociateUserInterface $associateUser
     *
     * @return AssociateUserInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(AssociateUserInterface $associateUser): AssociateUserInterface;

    /**
     * Get associate user by id
     *
     * @param int $id
     *
     * @return AssociateUserInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateUserInterface;

    /**
     * Get associate user by username
     *
     * @param string $username
     *
     * @return AssociateUserInterface
     * @throws NoSuchEntityException
     */
    public function getByUsername(string $username): AssociateUserInterface;

    /**
     * Retrieve associate users matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateUserSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateUserSearchResultsInterface;

    /**
     * Delete associate user
     *
     * @param AssociateUserInterface $associateUser
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(AssociateUserInterface $associateUser): void;

    /**
     * Delete associate user by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
