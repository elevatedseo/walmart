<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Api;

interface OperationTypeInterface
{
    public const NEW_ORDER = 'new_order';
    public const CANCEL_ORDER = 'cancel_order';
}
