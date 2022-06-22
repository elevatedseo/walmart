<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeSearchResultsInterface;

/**
 * Interface for Car Make Repository
 *
 * @api
 */
interface CarMakeRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return CarMakeInterface
     */
    public function create(): CarMakeInterface;

    /**
     * Save carmake
     *
     * @param CarMakeInterface $carmake
     *
     * @return CarMakeInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(CarMakeInterface $carmake): CarMakeInterface;

    /**
     * Get carmake by id
     *
     * @param int $id
     *
     * @return CarMakeInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): CarMakeInterface;

    /**
     * Retrieve carmakes matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return CarMakeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CarMakeSearchResultsInterface;

    /**
     * Delete carmake
     *
     * @param CarMakeInterface $carmake
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(CarMakeInterface $carmake): void;

    /**
     * Delete carmake by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
