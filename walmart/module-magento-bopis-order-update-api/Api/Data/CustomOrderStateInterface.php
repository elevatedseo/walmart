<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

/**
 * List of custom order Status and States
 */
interface CustomOrderStateInterface
{
    public const ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE = 'ship_to_store_pending';
    public const ORDER_STATUS_SHIP_TO_STORE_PENDING_LABEL = 'Ship To Store Pending';

    public const ORDER_STATUS_READY_FOR_PICKUP_CODE = 'ready_for_pickup';
    public const ORDER_STATUS_READY_FOR_PICKUP_LABEL = 'Ready for Pickup';

    public const ORDER_STATUS_DISPENSED_CODE = 'dispensed';
    public const ORDER_STATUS_DISPENSED_LABEL = 'Dispensed';
}
