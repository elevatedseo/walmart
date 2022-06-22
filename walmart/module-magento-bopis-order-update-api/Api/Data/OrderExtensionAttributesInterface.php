<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface Order Extension Attributes Model
 *
 * @api
 */
interface OrderExtensionAttributesInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';
    public const ORDER_ID = 'order_id';
    public const BOPIS_QUEUE_STATUS = 'bopis_queue_status';
    public const WMT_STS_EMAIL_STATUS = 'wmt_sts_email_status';
    public const WMT_IS_SHIP_TO_STORE = 'wmt_is_ship_to_store';

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
     * Get order ID
     *
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Set order ID
     *
     * @param  int $orderId
     * @return void
     */
    public function setOrderId(int $orderId): void;

    /**
     * Get bopis queue status
     *
     * @return int|null
     */
    public function getBopisQueueStatus(): ?int;

    /**
     * Set bopis queue status
     *
     * @param  int|null $bopisQueueStatus
     * @return void
     */
    public function setBopisQueueStatus(?int $bopisQueueStatus): void;

    /**
     * Get wmt sts email status
     *
     * @return int|null
     */
    public function getWmtStsEmailStatus(): ?int;

    /**
     * Set wmt sts email status
     *
     * @param  int|null $wmtStsEmailStatus
     * @return void
     */
    public function setWmtStsEmailStatus(?int $wmtStsEmailStatus): void;

    /**
     * Get wmt is ship to store
     *
     * @return int|null
     */
    public function getWmtIsShipToStore(): ?int;

    /**
     * Set wmt is ship to store
     *
     * @param  int|null $wmtIsShipToStore
     * @return void
     */
    public function setWmtIsShipToStore(?int $wmtIsShipToStore): void;
}
