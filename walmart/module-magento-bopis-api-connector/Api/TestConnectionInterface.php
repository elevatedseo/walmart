<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Api;

/**
 * Interface TestConnection
 *
 * @api
 */
interface TestConnectionInterface
{

    /**
     * Validate connection with Walmart API
     *
     * @api
     *
     * @param  string $environment
     * @param  string $clientId
     * @param  string $clientSecret
     * @return mixed
     */
    public function validateConnection(string $environment, string $clientId, string $clientSecret);
}
