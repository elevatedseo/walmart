<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api;

use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;

/**
 * Authenticate with google provider
 *
 * @api
 */
interface GoogleAuthenticateInterface
{
    /**
     * Verify google TFA otp
     *
     * @param string $otp
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface
     */
    public function execute(string $otp): LoginResponseInterface;
}
