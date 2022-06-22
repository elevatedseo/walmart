<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api;

use Walmart\BopisStoreAssociateApi\Api\Data\SessionVerifyResponseInterface;

interface SessionVerifyInterface
{
    /**
     * Verify session
     *
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\SessionVerifyResponseInterface
     */
    public function execute(): SessionVerifyResponseInterface;
}
