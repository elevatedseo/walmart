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
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleSearchResultsInterface;

/**
 * Interface for Associate Role Repository
 *
 * @api
 */
interface AssociateRoleRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return AssociateRoleInterface
     */
    public function create();

    /**
     * Save associate role
     *
     * @param AssociateRoleInterface $associateRole
     *
     * @return AssociateRoleInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(AssociateRoleInterface $associateRole): AssociateRoleInterface;

    /**
     * Get associate role by id
     *
     * @param int $id
     *
     * @return AssociateRoleInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): AssociateRoleInterface;

    /**
     * Retrieve associate roles matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return AssociateRoleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): AssociateRoleSearchResultsInterface;

    /**
     * Delete associate role
     *
     * @param AssociateRoleInterface $associateRole
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(AssociateRoleInterface $associateRole): void;

    /**
     * Delete associate role by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
