<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Associate Passwords Search Results
 *
 * @api
 */
interface AssociatePasswordsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Associate Passwords list
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface[]
     */
    public function getItems();

    /**
     * Set Associate Passwords list
     *
     * @param  \Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
