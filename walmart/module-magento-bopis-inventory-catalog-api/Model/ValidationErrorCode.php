<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Model;

class ValidationErrorCode
{
    /**
     * Error codes, that Walmart Bopis Inventory module can set to quote items
     */
    public const ERROR_QTY = 'qty';
    public const ERROR_DELIVERY_METHOD_DISABLED = 'delivery_method_disabled';
}
