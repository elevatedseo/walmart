<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;

/**
 * Perform store associate user authentication
 *
 * @api
 */
interface LoginInterface
{
    /**
     * authenticate store associate user
     *
     * @param string $username
     * @param string $password
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface
     */
    public function execute(string $username, string $password): LoginResponseInterface;
}
