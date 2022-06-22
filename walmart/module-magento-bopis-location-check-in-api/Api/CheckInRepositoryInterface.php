<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInSearchResultsInterface;

/**
 * Interface for CheckIn Repository
 *
 * @api
 */
interface CheckInRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return CheckInInterface
     */
    public function create(): CheckInInterface;

    /**
     * Save check-in
     *
     * @param CheckInInterface $checkIn
     *
     * @return CheckInInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(CheckInInterface $checkIn): CheckInInterface;

    /**
     * Get check-in by id
     *
     * @param int $id
     *
     * @return CheckInInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): CheckInInterface;

    /**
     * Get check-in by order ID
     *
     * @param int $orderId
     *
     * @return CheckInInterface
     * @throws NoSuchEntityException
     */
    public function getByOrderId(int $orderId): CheckInInterface;

    /**
     * Retrieve check-ins matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return CheckInSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CheckInSearchResultsInterface;

    /**
     * Delete check-in
     *
     * @param CheckInInterface $checkIn
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(CheckInInterface $checkIn): void;

    /**
     * Delete check-in by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
