<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

/**
 * Interface For CheckIn Model
 *
 * @api
 */
interface CheckInInterface
{
    public const CHECKIN_ID = 'check_in_id';
    public const ORDER_ID = 'order_id';
    public const EXTERNAL_ID = 'external_id';
    public const SOURCE_CODE = 'source_code';
    public const CUSTOMER_ID = 'customer_id';
    public const STATUS = 'status';
    public const ALTERNATE_PICKUP_CONTACT = 'alternate_pickup_contact';
    public const COMPLETED_BY = 'completed_by';
    public const PARKING_SPOT = 'parking_spot';
    public const CAR_MAKE = 'car_make';
    public const CAR_COLOR = 'car_color';
    public const ADDITIONAL_NOTES = 'additional_notes';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_STARTED = 'STARTED';
    public const STATUS_FINISHED = 'FINISHED';
    public const STATUS_MISSED = 'MISSED';
    public const STATUS_ERROR = 'ERROR';

    /**
     * @return int
     */
    public function getCheckInId(): int;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setCheckInId(int $id): self;

    /**
     * @return int
     */
    public function getOrderId(): int;

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId(int $orderId): self;

    /**
     * @return string
     */
    public function getExternalId(): string;

    /**
     * @param string $externalId
     *
     * @return $this
     */
    public function setExternalId(string $externalId): self;

    /**
     * @return string|null
     */
    public function getSourceCode(): ?string;

    /**
     * @param string|null $sourceCode
     *
     * @return mixed
     */
    public function setSourceCode(?string $sourceCode);

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * @return int|null
     */
    public function getAlternatePickupContact(): ?int;

    /**
     * @param int|null $alternatePickupContact
     *
     * @return $this
     */
    public function setAlternatePickupContact(?int $alternatePickupContact): self;

    /**
     * @return int|null
     */
    public function getCompletedBy(): ?int;

    /**
     * @param int|null $completedBy
     *
     * @return $this
     */
    public function setCompletedBy(?int $completedBy): self;

    /**
     * @return string|null
     */
    public function getParkingSpot(): ?string;

    /**
     * @param string|null $parkingSpot
     *
     * @return $this
     */
    public function setParkingSpot(?string $parkingSpot): self;

    /**
     * @return int|null
     */
    public function getCarMake(): ?int;

    /**
     * @param int|null $carMake
     *
     * @return $this
     */
    public function setCarMake(?int $carMake): self;

    /**
     * @return int|null
     */
    public function getCarColor(): ?int;

    /**
     * @param int|null $carColor
     *
     * @return $this
     */
    public function setCarColor(?int $carColor): self;

    /**
     * @return string|null
     */
    public function getAdditionalNotes(): ?string;

    /**
     * @param string|null $additionalNotes
     *
     * @return $this
     */
    public function setAdditionalNotes(?string $additionalNotes): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?string $updatedAt): self;
}
