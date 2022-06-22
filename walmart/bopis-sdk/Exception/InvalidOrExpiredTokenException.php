<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisSdk\Exception;

use Walmart\BopisSdk\SdkException;

/**
 * Thrown when token is expired
 */
class InvalidOrExpiredTokenException extends SdkException
{
}
