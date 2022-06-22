<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession as AssociateSessionResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Associate Session Model
 */
class AssociateSession extends AbstractExtensibleModel implements AssociateSessionInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AssociateSessionResourceModel::class);
    }

    /**
     * @return int
     */
    public function getSessionId(): int
    {
        return (int)$this->getData(self::SESSION_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setSessionId(int $id): void
    {
        $this->setData(self::SESSION_ID, $id);
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
    public function getToken(): string
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * @param  string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->setData(self::TOKEN, $token);
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return (int)$this->getData(self::STATUS);
    }

    /**
     * @param  int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->setData(self::STATUS, $status);
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
}
