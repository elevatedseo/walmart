<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Car Make Search Results
 *
 * @api
 */
interface CarMakeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Car Make list
     *
     * @return \Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface[]
     */
    public function getItems();

    /**
     * Set Car Make list
     *
     * @param  \Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
