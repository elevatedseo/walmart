<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsSearchResultsInterface;

/**
 * Interface for Order Line Items Repository
 *
 * @api
 */
interface OrderLineItemsRepositoryInterface
{
    /**
     * Initialize an empty model
     *
     * @return OrderLineItemsInterface
     */
    public function create();

    /**
     * Save order items line
     *
     * @param OrderLineItemsInterface $orderItemsLine
     *
     * @return OrderLineItemsInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function save(OrderLineItemsInterface $orderItemsLine): OrderLineItemsInterface;

    /**
     * Get order items line by id
     *
     * @param int $id
     *
     * @return OrderLineItemsInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): OrderLineItemsInterface;

    /**
     * Retrieve order items line matching the specified criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return OrderLineItemsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): OrderLineItemsSearchResultsInterface;

    /**
     * Delete order items line
     *
     * @param OrderLineItemsInterface $orderItemsLine
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(OrderLineItemsInterface $orderItemsLine): void;

    /**
     * Delete order items line by id
     *
     * @param int $id
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void;
}
