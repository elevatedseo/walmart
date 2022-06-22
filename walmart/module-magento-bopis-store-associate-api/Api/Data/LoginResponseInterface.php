<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

/**
 * Represents login response data
 *
 * @api
 */
interface LoginResponseInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const SESSION_TOKEN = 'session_token';
    public const SESSION_LIFETIME = 'session_lifetime';
    public const FIRSTNAME = 'first_name';
    public const LASTNAME = 'last_name';
    public const PERMISSIONS = 'permissions';
    public const ALL_LOCATIONS = 'all_locations';
    public const LOCATIONS = 'locations';
    public const PARAMETERS = 'parameters';

    /**
     * Get session token
     *
     * @return string
     */
    public function getSessionToken(): ?string;

    /**
     * Set session token
     *
     * @param  string $token
     * @return void
     */
    public function setSessionToken(string $token): void;

    /**
     * Get session lifetime
     *
     * @return int
     */
    public function getSessionLifetime(): ?int;

    /**
     * Set session lifetime
     *
     * @param  string $sessionLifetime
     * @return void
     */
    public function setSessionLifetime(int $sessionLifetime): void;

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Set firstname
     *
     * @param  string $firstname
     * @return void
     */
    public function setFirstName(string $firstname): void;

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Set lastname
     *
     * @param  string $lastname
     * @return void
     */
    public function setLastName(string $lastname): void;

    /**
     * Get permissions
     *
     * @return string[]
     */
    public function getPermissions(): ?array;

    /**
     * Set permissions
     *
     * @param  array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void;

    /**
     * Get all locations
     *
     * @return bool
     */
    public function getAllLocations(): ?bool;

    /**
     * Set all locations
     *
     * @param  bool $allLocations
     * @return void
     */
    public function setAllLocations(bool $allLocations): void;

    /**
     * Get locations
     *
     * @return string[]
     */
    public function getLocations(): ?array;

    /**
     * Set locations
     *
     * @param  array $locations
     * @return void
     */
    public function setLocations(array $locations): void;

    /**
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface
     */
    public function getParameters(): ParametersResponseInterface;

    /**
     * @param \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface $parameters
     *
     * @return void
     */
    public function setParameters(ParametersResponseInterface $parameters): void;
}
