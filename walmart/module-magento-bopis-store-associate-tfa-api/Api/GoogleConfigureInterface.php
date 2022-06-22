<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api;

use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleConfigureResponseInterface;

/**
 * Represents the google provider
 *
 * @api
 */
interface GoogleConfigureInterface
{
    /**
     * Get the information required to configure google
     *
     * @return \Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleConfigureResponseInterface
     */
    public function execute(): GoogleConfigureResponseInterface;
}
