<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Associate Passwords Model
 *
 * @api
 */
interface AssociatePasswordsInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const PASSWORD_ID = 'password_id';
    public const USER_ID = 'user_id';
    public const PASSWORD = 'password';
    public const IS_ACTIVE = 'is_active';
    public const UPDATED_AT = 'updated_at';

    public const NUMBER_OF_PASSWORDS_TO_STORE = 5;

    /**
     * Get password ID
     *
     * @return int
     */
    public function getPasswordId(): ?int;

    /**
     * Set password ID
     *
     * @param  int $id
     * @return void
     */
    public function setPasswordId(int $id): void;

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
     * Get password
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Set password
     *
     * @param  string $password
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     *
     * @param  bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void;

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
