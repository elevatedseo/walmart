<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Api;

/**
 * Interface TokenInterface
 *
 * @api
 */
interface TokenInterface
{
    /**
     * Get Walmart token
     *
     * @return mixed
     */
    public function getToken();
}
