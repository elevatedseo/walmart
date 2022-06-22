<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Associate Role Search Results
 *
 * @api
 */
interface AssociateRoleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Associate Role list
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface[]
     */
    public function getItems();

    /**
     * Set Associate Role list
     *
     * @param  \Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
