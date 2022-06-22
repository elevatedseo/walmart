<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Magento\Framework\DataObject;
use Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface;

/**
 * @api
 */
class Response extends DataObject implements ResponseInterface
{
    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage(string $message): ResponseInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getSuccess(): bool
    {
        return $this->getData(self::SUCCESS);
    }

    /**
     * @inheritdoc
     */
    public function setSuccess(bool $success): ResponseInterface
    {
        return $this->setData(self::SUCCESS, $success);
    }
}
