<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser as AssociateUserResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Associate User Model
 */
class AssociateUser extends AbstractExtensibleModel implements AssociateUserInterface
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @param Context                    $context
     * @param Registry                   $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory      $customAttributeFactory
     * @param EncryptorInterface         $encryptor
     * @param AbstractResource|null      $resource
     * @param AbstractDb|null            $resourceCollection
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        EncryptorInterface $encryptor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->encryptor = $encryptor;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AssociateUserResourceModel::class);
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->getData(self::USER_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setUserId(int $id): void
    {
        $this->setData(self::USER_ID, $id);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getData(self::USERNAME);
    }

    /**
     * @param  string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->setData(self::USERNAME, $username);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getData(self::PASSWORD);
    }

    /**
     * @param  string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $password = $this->encryptor->getHash($password, true);
        $this->setData(self::PASSWORD, $password);
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * @param  string|null $firstname
     * @return void
     */
    public function setFirstname(?string $firstname): void
    {
        $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->getData(self::LASTNAME);
    }

    /**
     * @param  string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * @return int|null
     */
    public function getRoleId(): ?int
    {
        return $this->getData(self::ROLE_ID) === null ?
            null :
            (int)$this->getData(self::ROLE_ID);
    }

    /**
     * @param  int|null $roleId
     * @return void
     */
    public function setRoleId(?int $roleId): void
    {
        $this->setData(self::ROLE_ID, $roleId);
    }

    /**
     * @return bool
     */
    public function getAllLocations(): bool
    {
        return (bool)$this->getData(self::ALL_LOCATIONS);
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
     * @return string
     */
    public function getUserLocale(): string
    {
        return $this->getData(self::USER_LOCALE);
    }

    /**
     * @param  string $locale
     * @return void
     */
    public function setUserLocale(string $locale): void
    {
        $this->setData(self::USER_LOCALE, $locale);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @param  bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return string|null
     */
    public function getActiveFrom(): ?string
    {
        return $this->getData(self::ACTIVE_FROM);
    }

    /**
     * @param  string|null $activeFrom
     * @return void
     */
    public function setActiveFrom(?string $activeFrom): void
    {
        $this->setData(self::ACTIVE_FROM, $activeFrom);
    }

    /**
     * @return string|null
     */
    public function getActiveTo(): ?string
    {
        return $this->getData(self::ACTIVE_TO);
    }

    /**
     * @param  string|null $activeTo
     * @return void
     */
    public function setActiveTo(?string $activeTo): void
    {
        $this->setData(self::ACTIVE_TO, $activeTo);
    }

    /**
     * @return string|null
     */
    public function getLastLoggedAt(): ?string
    {
        return $this->getData(self::LAST_LOGGED_AT);
    }

    /**
     * @param  string|null $lastLoggedAt
     * @return void
     */
    public function setLastLoggedAt(?string $lastLoggedAt): void
    {
        $this->setData(self::LAST_LOGGED_AT, $lastLoggedAt);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param  string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param  string|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @return int
     */
    public function getFailuresNum(): int
    {
        return (int)$this->getData(self::FAILURES_NUM);
    }

    /**
     * @param  int $failuresNum
     * @return void
     */
    public function setFailuresNum(int $failuresNum): void
    {
        $this->setData(self::FAILURES_NUM, $failuresNum);
    }

    /**
     * @return string|null
     */
    public function getLockExpires(): ?string
    {
        return $this->getData(self::LOCK_EXPIRES);
    }

    /**
     * @param  string|null $lockExpires
     * @return void
     */
    public function setLockExpires(?string $lockExpires): void
    {
        $this->setData(self::LOCK_EXPIRES, $lockExpires);
    }

    /**
     * @return bool
     */
    public function getPasswordGenerated(): bool
    {
        return (bool)$this->getData(self::PASSWORD_GENERATED);
    }

    /**
     * @param  bool $passwordGenerated
     * @return void
     */
    public function setPasswordGenerated(bool $passwordGenerated): void
    {
        $this->setData(self::PASSWORD_GENERATED, $passwordGenerated);
    }
}
