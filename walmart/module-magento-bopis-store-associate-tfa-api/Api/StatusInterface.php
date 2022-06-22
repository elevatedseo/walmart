<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api;

/**
 *
 * @api
 */
interface StatusInterface
{
    public const INVALID_OTP_CODE = 'invalid-otp-code';
    public const TFA_PROVIDER_NOT_ALLOWED = 'tfa-provider-not-allowed';
    public const TFA_AUTHORIZATION_NOT_AVAILABLE = 'tfa-authorization-not-available';
}
