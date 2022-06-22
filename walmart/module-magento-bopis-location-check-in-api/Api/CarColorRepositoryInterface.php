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
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorSearchResultsInterface;

/**
 * Interface for Car Color Repository
 *
 * @api
 */
interface CarColorRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return CarColorInterface
     */
    public function create(): CarColorInterface;

    /**
     * Save car color
     *
     * @param CarColorInterface $carColor
     *
     * @return CarColorInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(CarColorInterface $carColor): CarColorInterface;

    /**
     * Get car color by id
     *
     * @param int $id
     *
     * @return CarColorInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): CarColorInterface;

    /**
     * Retrieve car colors matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return CarColorSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CarColorSearchResultsInterface;

    /**
     * Delete car color
     *
     * @param CarColorInterface $carColor
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(CarColorInterface $carColor): void;

    /**
     * Delete car color by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
