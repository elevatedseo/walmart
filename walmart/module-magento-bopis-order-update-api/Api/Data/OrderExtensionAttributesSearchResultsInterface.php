<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Order Extension Attributes Search Results
 *
 * @api
 */
interface OrderExtensionAttributesSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Order Extension Attributes list
     *
     * @return \Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface[]
     */
    public function getItems();

    /**
     * Set Order Extension Attributes list
     *
     * @param  \Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
