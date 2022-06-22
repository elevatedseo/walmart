<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\DataObject;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface;

/**
 * @inheritdoc
 */
class LoginResponse extends DataObject implements LoginResponseInterface
{
    /**
     * @return string
     */
    public function getSessionToken(): string
    {
        return (string)$this->getDataByKey(self::SESSION_TOKEN);
    }

    /**
     * @param  string $sessionToken
     * @return void
     */
    public function setSessionToken(string $sessionToken): void
    {
        $this->setData(self::SESSION_TOKEN, $sessionToken);
    }

    /**
     * @return int
     */
    public function getSessionLifetime(): int
    {
        return (int)$this->getDataByKey(self::SESSION_LIFETIME);
    }

    /**
     * @param  int $sessionLifetime
     * @return void
     */
    public function setSessionLifetime(int $sessionLifetime): void
    {
        $this->setData(self::SESSION_LIFETIME, $sessionLifetime);
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return (string)$this->getDataByKey(self::FIRSTNAME);
    }

    /**
     * @param  string $firstname
     * @return void
     */
    public function setFirstName(string $firstname): void
    {
        $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return (string)$this->getDataByKey(self::LASTNAME);
    }

    /**
     * @param  string $lastname
     * @return void
     */
    public function setLastName(string $lastname): void
    {
        $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return (array)$this->getDataByKey(self::PERMISSIONS);
    }

    /**
     * @param  array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void
    {
        $this->setData(self::PERMISSIONS, $permissions);
    }

    /**
     * @return bool
     */
    public function getAllLocations(): bool
    {
        return (bool)$this->getDataByKey(self::ALL_LOCATIONS);
    }

    /**
     * @param  bool $allLocations
     * @return void
     */
    public function setAllLocations(bool $allLocations): void
    {
        $this->setData(self::ALL_LOCATIONS, $allLocations);
    }

    /**
     * @return string[]
     */
    public function getLocations(): array
    {
        return (array)$this->getDataByKey(self::LOCATIONS);
    }

    /**
     * @param  array $locations
     * @return void
     */
    public function setLocations(array $locations): void
    {
        $this->setData(self::LOCATIONS, $locations);
    }

    /**
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface
     */
    public function getParameters(): ParametersResponseInterface
    {
        return $this->getData(self::PARAMETERS);
    }

    /**
     * @param \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface $parameters
     *
     * @return void
     */
    public function setParameters(ParametersResponseInterface $parameters): void
    {
        $this->setData(self::PARAMETERS, $parameters);
    }
}
