<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Associate Session Model
 *
 * @api
 */
interface AssociateSessionInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const SESSION_ID = 'session_id';
    public const USER_ID = 'user_id';
    public const TOKEN = 'token';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Constants defined for status values
     */
    public const STATUS_ACTIVE = 1;
    public const STATUS_EXPIRED = 2;
    public const STATUS_TERMINATED = 3;
    public const STATUS_PASSWORD_EXPIRED = 4;
    public const STATUS_TFA_PASSED = 5;

    /**
     * Get session ID
     *
     * @return int
     */
    public function getSessionId(): int;

    /**
     * Set session ID
     *
     * @param  int $id
     * @return void
     */
    public function setSessionId(int $id): void;

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set user ID
     *
     * @param  int $userId
     * @return void
     */
    public function setUserId(int $userId): void;

    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Set token
     *
     * @param  string $token
     * @return void
     */
    public function setToken(string $token): void;

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus(): int;

    /**
     * Set status
     *
     * @param  int $status
     * @return void
     */
    public function setStatus(int $status): void;

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     *
     * @param  string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;

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
}
