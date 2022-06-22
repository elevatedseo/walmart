<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for CheckIn Search Results
 *
 * @api
 */
interface CheckInSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get CheckIn list
     *
     * @return \Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface[]
     */
    public function getItems();

    /**
     * Set CheckIn list
     *
     * @param  \Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
