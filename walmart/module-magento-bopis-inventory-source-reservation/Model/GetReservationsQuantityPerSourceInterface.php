<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Model;

/**
 * Responsible for retrieving reservation quantity per source (without stock data)
 *
 * @api
 */
interface GetReservationsQuantityPerSourceInterface
{
    /**
     * Given a product sku and source code, return reservation quantity
     *
     * @param  string $sku
     * @param  string $sourceCode
     * @return float
     */
    public function execute(string $sku, string $sourceCode): float;
}
