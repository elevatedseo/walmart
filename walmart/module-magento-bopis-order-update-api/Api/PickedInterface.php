<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api;

use Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface;

/**
 * @api
 * Interface PickedInterface
 */
interface PickedInterface
{
    /**
     * Order Updates - Ready for Pickup
     *
     * @param string $orderId
     * @param string $orderSource
     * @param mixed  $fulfilmentLines
     * @param mixed  $pickingSection
     *
     * @return \Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface
     */
    public function execute(string $orderId, string $orderSource, $fulfilmentLines, $pickingSection): ResponseInterface;
}
