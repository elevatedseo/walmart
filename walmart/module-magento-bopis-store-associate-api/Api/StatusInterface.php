<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

interface StatusInterface
{
    public const INCORRECT_USERNAME_PASSWORD = 'incorrect-username-password';
    public const USER_ACCOUNT_LOCKED = 'user-account-locked';
    public const USER_ACCOUNT_NOT_ACTIVE = 'user-account-not-active';
    public const UNAUTHORIZED_CONSUMER = 'unauthorized-consumer';
    public const SESSION_TOKEN_MISSING = 'session-token-missing';
    public const SESSION_TOKEN_INVALID = 'session-token-invalid';
    public const NEW_PASSWORD_INSUFFICIENT = 'new-password-insufficient';
    public const WRONG_PASSWORD = 'wrong-password';
    public const SESSION_NOT_FOUND_INVALID = 'session-not-found-invalid';
}
