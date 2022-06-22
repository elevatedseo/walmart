<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Model;

use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueExtensionInterface;

class BopisQueue extends \Magento\Framework\Model\AbstractModel implements BopisQueueInterface
{
    /**
     * @var BopisQueueExtensionInterface
     */
    private BopisQueueExtensionInterface $extensionAttributes;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Walmart\BopisOperationQueueApi\Model\ResourceModel\BopisQueue::class);
    }

    /**
     * Get bopis_queue_id
     *
     * @return string|null
     */
    public function getBopisQueueId()
    {
        return $this->getData(self::BOPIS_QUEUE_ID);
    }

    /**
     * Set bopis_queue_id
     *
     * @param string $bopisQueueId
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setBopisQueueId($bopisQueueId)
    {
        return $this->setData(self::BOPIS_QUEUE_ID, $bopisQueueId);
    }

    /**
     * Get queue_id
     *
     * @return string|null
     */
    public function getQueueId()
    {
        return $this->getData(self::QUEUE_ID);
    }

    /**
     * Set queue_id
     *
     * @param string $queueId
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setQueueId($queueId)
    {
        return $this->setData(self::QUEUE_ID, $queueId);
    }

    /**
     * Get entity_type
     *
     * @return string|null
     */
    public function getEntityType()
    {
        return $this->getData(self::ENTITY_TYPE);
    }

    /**
     * Set entity_type
     *
     * @param string $entityType
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * Get entity_id
     *
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     *
     * @param string $entityId
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get error_code
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->getData(self::ERROR_CODE);
    }

    /**
     * Set error_code
     *
     * @param string $errorCode
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setErrorCode($errorCode)
    {
        return $this->setData(self::ERROR_CODE, $errorCode);
    }

    /**
     * Get total_retries
     *
     * @return string|null
     */
    public function getTotalRetries()
    {
        return $this->getData(self::TOTAL_RETRIES);
    }

    /**
     * Set total_retries
     *
     * @param string $totalRetries
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setTotalRetries($totalRetries)
    {
        return $this->setData(self::TOTAL_RETRIES, $totalRetries);
    }

    /**
     * Get error_message
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * Set error_message
     *
     * @param string $errorMessage
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getOperationType(): ?string
    {
        return $this->getData(self::OPERATION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setOperationType(string $operationType): BopisQueueInterface
    {
        return $this->setData(self::OPERATION_TYPE, $operationType);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?BopisQueueExtensionInterface
    {
        if (isset($this->extensionAttributes)) {
            return $this->extensionAttributes;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(BopisQueueExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }
}
