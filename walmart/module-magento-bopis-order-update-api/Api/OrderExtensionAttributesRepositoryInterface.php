<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesSearchResultsInterface;

/**
 * Interface for Order Extension Attributes Repository
 *
 * @api
 */
interface OrderExtensionAttributesRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return OrderExtensionAttributesInterface
     */
    public function create(): OrderExtensionAttributesInterface;

    /**
     * Save order extension attributes
     *
     * @param OrderExtensionAttributesInterface $orderExtensionAttributes
     *
     * @return OrderExtensionAttributesInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(
        OrderExtensionAttributesInterface $orderExtensionAttributes
    ): OrderExtensionAttributesInterface;

    /**
     * Get order extension attributes by id
     *
     * @param int $id
     *
     * @return OrderExtensionAttributesInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderExtensionAttributesInterface;

    /**
     * Retrieve order extension attributes matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderExtensionAttributesSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): OrderExtensionAttributesSearchResultsInterface;

    /**
     * Delete order extension attributes
     *
     * @param OrderExtensionAttributesInterface $orderExtensionAttributes
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(OrderExtensionAttributesInterface $orderExtensionAttributes): void;

    /**
     * Delete order extension attributes by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
