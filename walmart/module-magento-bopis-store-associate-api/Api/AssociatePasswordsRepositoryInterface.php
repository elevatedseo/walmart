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
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsSearchResultsInterface;

/**
 * Interface for Associate Passwords Repository
 *
 * @api
 */
interface AssociatePasswordsRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return AssociatePasswordsInterface
     */
    public function create();

    /**
     * Save associate password
     *
     * @param AssociatePasswordsInterface $associatePassword
     *
     * @return AssociatePasswordsInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(AssociatePasswordsInterface $associatePassword): AssociatePasswordsInterface;

    /**
     * Get associate password by id
     *
     * @param int $id
     *
     * @return AssociatePasswordsInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociatePasswordsInterface;

    /**
     * Retrieve associate passwords matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociatePasswordsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociatePasswordsSearchResultsInterface;

    /**
     * Delete associate password
     *
     * @param AssociatePasswordsInterface $associatePassword
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(AssociatePasswordsInterface $associatePassword): void;

    /**
     * Delete associate password by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
