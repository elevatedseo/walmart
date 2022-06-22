<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Request Home Delivery service availability for given SKU and Qty
 *
 * @api
 */
interface RequestInterface extends ExtensibleDataInterface
{
    public const KEY_ITEMS = 'items';

    /**
     * Set Items
     *
     * @param \Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterface[] $items
     * @return void
     */
    public function setItems(array $items): void;

    /**
     * Get requested Items
     *
     * @return \Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterface[]
     */
    public function getItems(): array;
}
