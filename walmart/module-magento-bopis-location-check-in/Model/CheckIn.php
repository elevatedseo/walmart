<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CheckIn as CheckInResourceModel;

class CheckIn extends AbstractModel implements CheckInInterface
{
    protected $_eventPrefix = 'check_in';

    protected $_eventObject = 'check_in';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(CheckInResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getCheckInId(): int
    {
        return (int)$this->getData(self::CHECKIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCheckInId(int $id): CheckInInterface
    {
        return $this->setData(self::CHECKIN_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $orderId): CheckInInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(): string
    {
        return (string)$this->getData(self::EXTERNAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExternalId(string $externalId): CheckInInterface
    {
        return $this->setData(self::EXTERNAL_ID, $externalId);
    }

    /**
     * @inheritDoc
     */
    public function getSourceCode(): ?string
    {
        $sourceCode = $this->getData(self::SOURCE_CODE);

        return !empty($sourceCode) ? $sourceCode : null;
    }

    /**
     * @inheritDoc
     */
    public function setSourceCode(?string $sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): CheckInInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): CheckInInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getAlternatePickupContact(): ?int
    {
        $alternatePickupContact = $this->getData(self::ALTERNATE_PICKUP_CONTACT);

        return !empty($alternatePickupContact) ? (int)$alternatePickupContact : null;
    }

    /**
     * @inheritDoc
     */
    public function setAlternatePickupContact(?int $alternatePickupContact): CheckInInterface
    {
        return $this->setData(self::ALTERNATE_PICKUP_CONTACT, $alternatePickupContact);
    }

    /**
     * @inheritDoc
     */
    public function getCompletedBy(): ?int
    {
        $completedBy = $this->getData(self::COMPLETED_BY);

        return !empty($completedBy) ? (int)$completedBy : null;
    }

    /**
     * @inheritDoc
     */
    public function setCompletedBy(?int $completedBy): CheckInInterface
    {
        return $this->setData(self::COMPLETED_BY, $completedBy);
    }

    /**
     * @inheritDoc
     */
    public function getParkingSpot(): ?string
    {
        $parkingSpot = $this->getData(self::PARKING_SPOT);

        return !empty($parkingSpot) ? (string)$parkingSpot : null;
    }

    /**
     * @inheritDoc
     */
    public function setParkingSpot(?string $parkingSpot): CheckInInterface
    {
        return $this->setData(self::PARKING_SPOT, $parkingSpot);
    }

    /**
     * @inheritDoc
     */
    public function getCarMake(): ?int
    {
        $carMake = $this->getData(self::CAR_MAKE);

        return !empty($carMake) ? (int)$carMake : null;
    }

    /**
     * @inheritDoc
     */
    public function setCarMake(?int $carMake): CheckInInterface
    {
        return $this->setData(self::CAR_MAKE, $carMake);
    }

    /**
     * @inheritDoc
     */
    public function getCarColor(): ?int
    {
        $carColor = $this->getData(self::CAR_COLOR);

        return !empty($carColor) ? (int)$carColor : null;
    }

    /**
     * @inheritDoc
     */
    public function setCarColor(?int $carColor): CheckInInterface
    {
        return $this->setData(self::CAR_COLOR, $carColor);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalNotes(): ?string
    {
        $additionalNotes = $this->getData(self::ADDITIONAL_NOTES);

        return !empty($additionalNotes) ? (string)$additionalNotes : null;
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalNotes(?string $additionalNotes): CheckInInterface
    {
        return $this->setData(self::ADDITIONAL_NOTES, $additionalNotes);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): CheckInInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        $updatedAt = $this->getData(self::UPDATED_AT);

        return !empty($updatedAt) ? (string)$updatedAt : null;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(?string $updatedAt): CheckInInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
