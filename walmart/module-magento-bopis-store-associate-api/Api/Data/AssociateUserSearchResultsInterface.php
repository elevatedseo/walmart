<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Associate User Search Results
 *
 * @api
 */
interface AssociateUserSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Associate User list
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface[]
     */
    public function getItems();

    /**
     * Set Associate User list
     *
     * @param  \Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
