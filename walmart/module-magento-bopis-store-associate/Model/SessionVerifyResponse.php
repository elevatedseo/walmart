<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\DataObject;
use Walmart\BopisStoreAssociateApi\Api\Data\SessionVerifyResponseInterface;

class SessionVerifyResponse extends DataObject implements SessionVerifyResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getResult(): ?bool
    {
        return (bool)$this->getDataByKey(self::RESULT);
    }

    /**
     * @inheritDoc
     */
    public function setResult(bool $result): void
    {
        $this->setData(self::RESULT, $result);
    }
}
