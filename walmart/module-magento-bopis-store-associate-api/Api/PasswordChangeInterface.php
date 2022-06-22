<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

use Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterface;

/**
 * Perform password change action for store associate user
 *
 * @api
 */
interface PasswordChangeInterface
{
    /**
     * Password change action
     *
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterface
     */
    public function execute(string $oldPassword, string $newPassword): PasswordChangeResponseInterface;
}
