<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Exception;

use Exception;

/**
 * Thrown if two-factor is not available
 */
class TwoFactorNotAvailable extends Exception
{
}
