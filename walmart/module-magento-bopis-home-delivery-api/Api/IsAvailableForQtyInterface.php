<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Api;

use Walmart\BopisHomeDeliveryApi\Api\Data\ItemRequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterface;

/**
 * Return is Home Delivery Available for requested items
 */
interface IsAvailableForQtyInterface
{
    /**
     * @param RequestInterface $request
     * @param bool $collectCartData
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterface
     */
    public function execute(RequestInterface $request, bool $collectCartData = true): ResultInterface;
}
