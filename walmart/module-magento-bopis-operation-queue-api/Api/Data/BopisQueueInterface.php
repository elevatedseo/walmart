<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface BopisQueueInterface
 */
interface BopisQueueInterface extends ExtensibleDataInterface
{
    public const STATUS = 'status';
    public const QUEUE_ID = 'queue_id';
    public const ERROR_CODE = 'error_code';
    public const TOTAL_RETRIES = 'total_retries';
    public const BOPIS_QUEUE_ID = 'bopis_queue_id';
    public const ENTITY_ID = 'entity_id';
    public const ENTITY_TYPE = 'entity_type';
    public const ERROR_MESSAGE = 'error_message';
    public const OPERATION_TYPE = 'operation_type';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get bopis_queue_id
     *
     * @return string|null
     */
    public function getBopisQueueId();

    /**
     * Set bopis_queue_id
     *
     * @param string $bopisQueueId
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setBopisQueueId($bopisQueueId);

    /**
     * Get queue_id
     *
     * @return string|null
     */
    public function getQueueId();

    /**
     * Set queue_id
     *
     * @param string $queueId
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setQueueId($queueId);

    /**
     * Get entity_type
     *
     * @return string|null
     */
    public function getEntityType();

    /**
     * Set entity_type
     *
     * @param string $entityType
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setEntityType($entityType);

    /**
     * Get entity_id
     *
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param string $entityId
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setEntityId($entityId);

    /**
     * Get error_code
     *
     * @return string|null
     */
    public function getErrorCode();

    /**
     * Set error_code
     *
     * @param string $errorCode
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setErrorCode($errorCode);

    /**
     * Get total_retries
     *
     * @return string|null
     */
    public function getTotalRetries();

    /**
     * Set total_retries
     *
     * @param string $totalRetries
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setTotalRetries($totalRetries);

    /**
     * Get error_message
     *
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Set error_message
     *
     * @param string $errorMessage
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setErrorMessage($errorMessage);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     *
     * @return \Api\Data\BopisQueueInterface
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getOperationType(): ?string;

    /**
     * @param string $operationType
     *
     * @return $this
     */
    public function setOperationType(string $operationType): self;

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void;

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set updated at
     *
     * @param  string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void;

    /**
     * Set Extension Attributes for Bopis Queue.
     *
     * @param \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueExtensionInterface|null $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(
        \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueExtensionInterface $extensionAttributes
    ): void;

    /**
     * Get Extension Attributes of Bopis Queue.
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueExtensionInterface|null
     */
    public function getExtensionAttributes(): ?BopisQueueExtensionInterface;
}
