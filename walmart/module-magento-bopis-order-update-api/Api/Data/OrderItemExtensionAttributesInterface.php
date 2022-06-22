<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface Order Item Extension Attributes Model
 *
 * @api
 */
interface OrderItemExtensionAttributesInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ITEM_ID = 'order_item_id';
    public const WMT_SHIP_TO_STORE = 'wmt_ship_to_store';
    public const WMT_ITEM_PICKED_STATUS = 'wmt_item_picked_status';
    public const WMT_ITEM_DISPENSED_STATUS = 'wmt_item_dispensed_status';

    /**
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Set entity ID
     *
     * @param  int $id
     * @return void
     */
    public function setEntityId(int $id): void;

    /**
     * Get order item ID
     *
     * @return int
     */
    public function getOrderItemId(): int;

    /**
     * Set order item ID
     *
     * @param  int $orderItemId
     * @return void
     */
    public function setOrderItemId(int $orderItemId): void;

    /**
     * Get wmt ship to store
     *
     * @return int|null
     */
    public function getWmtShipToStore(): ?int;

    /**
     * Set wmt ship to store
     *
     * @param  int|null $wmtShipToStore
     * @return void
     */
    public function setWmtShipToStore(?int $wmtShipToStore): void;

    /**
     * Get wmt item picked status
     *
     * @return string|null
     */
    public function getWmtItemPickedStatus(): ?string;

    /**
     * Set wmt item picked status
     *
     * @param  string|null $wmtItemPickedStatus
     * @return void
     */
    public function setWmtItemPickedStatus(?string $wmtItemPickedStatus): void;

    /**
     * Get wmt item dispensed status
     *
     * @return string|null
     */
    public function getWmtItemDispensedStatus(): ?string;

    /**
     * Set wmt item dispensed status
     *
     * @param  string|null $wmtItemDispensedStatus
     * @return void
     */
    public function setWmtItemDispensedStatus(?string $wmtItemDispensedStatus): void;
}
