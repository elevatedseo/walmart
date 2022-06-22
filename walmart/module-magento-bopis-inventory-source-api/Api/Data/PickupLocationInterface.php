<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Represents sources projection on for Store Pickup context.
 * Realisation must follow immutable DTO concept.
 * Partial immutability done according to restriction of current Extension Attributes implementation.
 *
 * @api
 */
interface PickupLocationInterface extends ExtensibleDataInterface
{
    public const ALLOW_SHIP_TO_STORE = 'allow_ship_to_store';
    public const USE_AS_SHIPPING_SOURCE = 'use_as_shipping_source';
    public const STORE_PICKUP_ENABLED = 'store_pickup_enabled';
    public const PICKUP_LEAD_TIME = 'pickup_lead_time';
    public const PICKUP_TIME_LABEL = 'pickup_time_label';
    public const CURBSIDE_ENABLED = 'curbside_enabled';
    public const TIMEZONE = 'timezone';
    public const STORE_PICKUP_INSTRUCTIONS = 'store_pickup_instructions';
    public const CURBSIDE_INSTRUCTIONS = 'curbside_instructions';
    public const PARKING_SPOTS_ENABLED = 'parking_spots_enabled';
    public const PARKING_SPOT_MANDATORY = 'parking_spot_mandatory';
    public const CUSTOM_PARKING_SPOT_ENABLED = 'custom_parking_spot_enabled';
    public const USE_CAR_COLOR = 'use_car_color';
    public const CAR_COLOR_MANDATORY = 'car_color_mandatory';
    public const USE_CAR_MAKE = 'use_car_make';
    public const CAR_MAKE_MANDATORY = 'car_make_mandatory';
    public const USE_ADDITIONAL_INFORMATION = 'use_additional_information';
    public const ADDITIONAL_INFORMATION_MANDATORY = 'additional_information_mandatory';
}
