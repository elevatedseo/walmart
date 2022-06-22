<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Order Line Items Search Results
 *
 * @api
 */
interface OrderLineItemsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Order Line Items list
     *
     * @return \Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface[]
     */
    public function getItems();

    /**
     * Set Order Line Items list
     *
     * @param  \Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
