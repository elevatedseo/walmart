<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Associate Tfa Config Search Results
 *
 * @api
 */
interface AssociateTfaConfigSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Associate Tfa Config list
     *
     * @return \Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface[]
     */
    public function getItems();

    /**
     * Set Associate Tfa Config list
     *
     * @param  \Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
