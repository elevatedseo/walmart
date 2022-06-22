<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

/**
 * Represents password change response data
 *
 * @api
 */
interface PasswordChangeResponseInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const PARAMETERS = 'parameters';
    public const SUCCESS = 'success';
    public const STATUS  = 'status';
    public const MESSAGE = 'message';

    /**
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface
     */
    public function getParameters(): ParametersResponseInterface;

    /**
     * @param \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface $parameters
     *
     * @return void
     */
    public function setParameters(ParametersResponseInterface $parameters): void;
}
