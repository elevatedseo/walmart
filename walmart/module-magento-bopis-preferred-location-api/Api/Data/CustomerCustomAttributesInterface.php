<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Api\Data;

/**
 * List of custom Customer attributes
 */
interface CustomerCustomAttributesInterface
{
    public const SELECTED_INVENTORY_SOURCE = 'selected_inventory_source';
    public const PREFERRED_METHOD = 'preferred_method';
}
