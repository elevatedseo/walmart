<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

class State
{
    public const PENDING_CANCELLATION_LABEL = 'Pending Cancellation';
    public const PENDING_CANCELLATION = 'pending_cancellation';
    public const ACKNOWLEDGED_CANCELLATION = 'ack_cancellation';
}
