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
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesSearchResultsInterface;

/**
 * Interface for Order Item Extension Attributes Repository
 *
 * @api
 */
interface OrderItemExtensionAttributesRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return OrderItemExtensionAttributesInterface
     */
    public function create(): OrderItemExtensionAttributesInterface;

    /**
     * Save order item extension attributes
     *
     * @param OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
     *
     * @return OrderItemExtensionAttributesInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(
        OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
    ): OrderItemExtensionAttributesInterface;

    /**
     * Get order item extension attributes by id
     *
     * @param int $id
     *
     * @return OrderItemExtensionAttributesInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderItemExtensionAttributesInterface;

    /**
     * Retrieve order item extension attributes matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderItemExtensionAttributesSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): OrderItemExtensionAttributesSearchResultsInterface;

    /**
     * Delete order item extension attributes
     *
     * @param OrderItemExtensionAttributesInterface $orderItemExtensionAttributes
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(OrderItemExtensionAttributesInterface $orderItemExtensionAttributes): void;

    /**
     * Delete order item extension attributes by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
