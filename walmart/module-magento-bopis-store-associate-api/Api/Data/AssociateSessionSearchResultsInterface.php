<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Associate Session Search Results
 *
 * @api
 */
interface AssociateSessionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Associate Session list
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface[]
     */
    public function getItems();

    /**
     * Set Associate Session list
     *
     * @param  \Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
