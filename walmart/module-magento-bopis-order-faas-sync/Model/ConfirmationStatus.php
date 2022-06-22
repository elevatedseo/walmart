<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Framework\DataObject;
use Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface;
use Walmart\BopisOrderFaasSync\Api\ConfirmationStatusInterface;

class ConfirmationStatus extends DataObject implements ConfirmationStatusInterface
{
    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): void
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getReason(): ?ReasonInterface
    {
        return $this->getData(self::REASON);
    }

    /**
     * @inheritDoc
     */
    public function setReason(?ReasonInterface $reason): void
    {
        $this->setData(self::REASON, $reason);
    }
}
