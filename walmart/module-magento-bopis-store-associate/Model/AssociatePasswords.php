<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords as AssociatePasswordsResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Associate Passwords Model
 */
class AssociatePasswords extends AbstractExtensibleModel implements AssociatePasswordsInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AssociatePasswordsResourceModel::class);
    }

    /**
     * @return int
     */
    public function getPasswordId(): int
    {
        return (int)$this->getData(self::PASSWORD_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setPasswordId(int $id): void
    {
        $this->setData(self::PASSWORD_ID, $id);
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
        $this->setData(self::PASSWORD, $password);
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
}
