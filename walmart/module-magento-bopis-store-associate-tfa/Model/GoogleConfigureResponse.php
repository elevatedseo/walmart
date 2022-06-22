<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\DataObject;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleConfigureResponseInterface;

/**
 * @inheritdoc
 */
class GoogleConfigureResponse extends DataObject implements GoogleConfigureResponseInterface
{
    /**
     * @return string
     */
    public function getQrBase64Image(): string
    {
        return (string)$this->getDataByKey(self::QR_BASE64_IMAGE);
    }

    /**
     * @param  string $qrBase64Image
     * @return void
     */
    public function setQrBase64Image(string $qrBase64Image): void
    {
        $this->setData(self::QR_BASE64_IMAGE, $qrBase64Image);
    }

    /**
     * @return string
     */
    public function getSecretCode(): string
    {
        return (string)$this->getDataByKey(self::SECRET_CODE);
    }

    /**
     * @param  string $secretCode
     * @return void
     */
    public function setSecretCode(string $secretCode): void
    {
        $this->setData(self::SECRET_CODE, $secretCode);
    }
}
