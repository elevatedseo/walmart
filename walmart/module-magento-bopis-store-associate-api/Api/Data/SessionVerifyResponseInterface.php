<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

/**
 * Represents session verify response data
 *
 * @api
 */
interface SessionVerifyResponseInterface
{
    public const RESULT = 'result';

    /**
     * Get result
     *
     * @return bool|null
     */
    public function getResult(): ?bool;

    /**
     * Set result
     *
     * @param bool $result
     *
     * @return void
     */
    public function setResult(bool $result): void;
}
