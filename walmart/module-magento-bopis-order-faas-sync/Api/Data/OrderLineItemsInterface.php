<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Order Line Items Model
 *
 * @api
 */
interface OrderLineItemsInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';
    public const LINE_ITEM = 'line_item';
    public const ORDER_INCREMENT_ID = 'order_increment_id';
    public const ORDER_ITEM_ID = 'order_item_id';

    /**
     * Get ID
     *
     * @return int
     */
    public function getEntityId(): ?int;

    /**
     * Set ID
     *
     * @param  int $id
     * @return void
     */
    public function setEntityId(int $id): void;

    /**
     * Get line item
     *
     * @return int
     */
    public function getLineItem(): int;

    /**
     * Set line item
     *
     * @param  int $lineItem
     * @return void
     */
    public function setLineItem(int $lineItem): void;

    /**
     * Get order increment id
     *
     * @return string
     */
    public function getOrderIncrementId(): string;

    /**
     * Set order increment id
     *
     * @param  string $orderIncrementId
     * @return void
     */
    public function setOrderIncrementId(string $orderIncrementId): void;

    /**
     * Get order item id
     *
     * @return int
     */
    public function getOrderItemId(): int;

    /**
     * Set order item id
     *
     * @param  int $orderItemId
     * @return void
     */
    public function setOrderItemId(int $orderItemId): void;
}
