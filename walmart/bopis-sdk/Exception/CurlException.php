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
 * Thrown if an error was encountered when posting order to the API
 */
class CurlException extends SdkException
{
}
