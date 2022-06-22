<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Order Item Extension Attributes Search Results
 *
 * @api
 */
interface OrderItemExtensionAttributesSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Order Item Extension Attributes list
     *
     * @return \Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface[]
     */
    public function getItems();

    /**
     * Set Order Item Extension Attributes list
     *
     * @param  \Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
