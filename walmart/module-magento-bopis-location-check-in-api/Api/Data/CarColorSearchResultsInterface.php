<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Car Color Search Results
 *
 * @api
 */
interface CarColorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Car Color list
     *
     * @return \Walmart\BopisLocationCheckInApi\Api\Data\CarColorInterface[]
     */
    public function getItems();

    /**
     * Set Car Color list
     *
     * @param  \Walmart\BopisLocationCheckInApi\Api\Data\CarColorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
