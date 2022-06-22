<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\DataObject;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface;

class ParametersResponse extends DataObject implements ParametersResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getSuccess(): ?bool
    {
        return (bool)$this->getDataByKey(self::SUCCESS);
    }

    /**
     * @inheritDoc
     */
    public function setSuccess(bool $success): void
    {
        $this->setData(self::SUCCESS, $success);
    }

    /**
     * @return string|null
     */
    public function getPasswordExpires(): ?string
    {
        $data = $this->getDataByKey(self::PASSWORD_EXPIRES);
        if (empty($data)) {
            return null;
        }

        return (string)$data;
    }

    /**
     * @param  string|null $passwordExpires
     * @return void
     */
    public function setPasswordExpires(?string $passwordExpires): void
    {
        $this->setData(self::PASSWORD_EXPIRES, $passwordExpires);
    }

    /**
     * @return bool|null
     */
    public function getPasswordChangeRequired(): ?bool
    {
        $data = $this->getDataByKey(self::PASSWORD_CHANGE_REQUIRED);
        if ($data === null) {
            return null;
        }

        return (bool)$data;
    }

    /**
     * @param  bool|null $passwordChangeRequired
     * @return void
     */
    public function setPasswordChangeRequired(?bool $passwordChangeRequired): void
    {
        $this->setData(self::PASSWORD_CHANGE_REQUIRED, $passwordChangeRequired);
    }
}
