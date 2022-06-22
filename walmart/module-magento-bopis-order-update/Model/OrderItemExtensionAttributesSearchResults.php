<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model;

use Magento\Framework\Api\SearchResults;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesSearchResultsInterface;

class OrderItemExtensionAttributesSearchResults extends SearchResults
    implements OrderItemExtensionAttributesSearchResultsInterface
{
}
