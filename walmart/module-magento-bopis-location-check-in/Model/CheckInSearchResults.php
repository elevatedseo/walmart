<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\Api\SearchResults;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInSearchResultsInterface;

class CheckInSearchResults extends SearchResults implements CheckInSearchResultsInterface
{
}
