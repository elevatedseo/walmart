<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Associate User Model
 *
 * @api
 */
interface AssociateUserInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const USER_ID = 'user_id';
    public const USERNAME = 'username';
    public const PASSWORD = 'password';
    public const FIRSTNAME = 'firstname';
    public const LASTNAME = 'lastname';
    public const ROLE_ID = 'role_id';
    public const ALL_LOCATIONS = 'all_locations';
    public const USER_LOCALE = 'user_locale';
    public const IS_ACTIVE = 'is_active';
    public const ACTIVE_FROM = 'active_from';
    public const ACTIVE_TO = 'active_to';
    public const LAST_LOGGED_AT = 'last_logged_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const FAILURES_NUM = 'failures_num';
    public const LOCK_EXPIRES = 'lock_expires';
    public const PASSWORD_GENERATED = 'password_generated';

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set user ID
     *
     * @param  int $id
     * @return void
     */
    public function setUserId(int $id): void;

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Set username
     *
     * @param  string $username
     * @return void
     */
    public function setUsername(string $username): void;

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
     * Get firstname
     *
     * @return string|null
     */
    public function getFirstname(): ?string;

    /**
     * Set firstname
     *
     * @param  string|null $firstname
     * @return void
     */
    public function setFirstname(?string $firstname): void;

    /**
     * Get lastname
     *
     * @return string|null
     */
    public function getLastname(): ?string;

    /**
     * Set lastname
     *
     * @param  string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void;

    /**
     * Get role ID
     *
     * @return int|null
     */
    public function getRoleId(): ?int;

    /**
     * Set role ID
     *
     * @param  int|null $roleId
     * @return void
     */
    public function setRoleId(?int $roleId): void;

    /**
     * Get all locations
     *
     * @return bool
     */
    public function getAllLocations(): bool;

    /**
     * Set all locations
     *
     * @param  bool $allLocations
     * @return void
     */
    public function setAllLocations(bool $allLocations): void;

    /**
     * Get user locale
     *
     * @return string
     */
    public function getUserLocale(): string;

    /**
     * Set user locale
     *
     * @param  string $locale
     * @return void
     */
    public function setUserLocale(string $locale): void;

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
     * Get active from
     *
     * @return string|null
     */
    public function getActiveFrom(): ?string;

    /**
     * Set active from
     *
     * @param  string|null $activeFrom
     * @return void
     */
    public function setActiveFrom(?string $activeFrom): void;

    /**
     * Get active to
     *
     * @return string|null
     */
    public function getActiveTo(): ?string;

    /**
     * Set active to
     *
     * @param  string|null $activeTo
     * @return void
     */
    public function setActiveTo(?string $activeTo): void;

    /**
     * Get last logged at
     *
     * @return string|null
     */
    public function getLastLoggedAt(): ?string;

    /**
     * Set last logged at
     *
     * @param  string|null $lastLoggedAt
     * @return void
     */
    public function setLastLoggedAt(?string $lastLoggedAt): void;

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

    /**
     * Get failures num
     *
     * @return int
     */
    public function getFailuresNum(): int;

    /**
     * Set failures num
     *
     * @param  int $failuresNum
     * @return void
     */
    public function setFailuresNum(int $failuresNum): void;

    /**
     * Get lock expires
     *
     * @return string|null
     */
    public function getLockExpires(): ?string;

    /**
     * Set lock expires
     *
     * @param  string|null $lockExpires
     * @return void
     */
    public function setLockExpires(?string $lockExpires): void;

    /**
     * Get password generated
     *
     * @return bool
     */
    public function getPasswordGenerated(): bool;

    /**
     * Set password generated
     *
     * @param  bool $passwordGenerated
     * @return void
     */
    public function setPasswordGenerated(bool $passwordGenerated): void;
}
