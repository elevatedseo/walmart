<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Model\Request;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultExtensionInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface;

/**
 * Result body
 */
class Result extends AbstractExtensibleModel implements ResultInterface
{
    /**
     * @inheritDoc
     */
    public function addOutOfStockItem(ResultItemInterface $item): ResultInterface
    {
        $this->_data[self::KEY_OUT_OF_STOCK_ITEMS][] = $item;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOutOfStockItems(array $items): ResultInterface
    {
        $this->_data[self::KEY_OUT_OF_STOCK_ITEMS] = $items;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutOfStockItems(): array
    {
        return $this->_data[self::KEY_OUT_OF_STOCK_ITEMS];
    }

    /**
     * @inheritDoc
     */
    public function setResult(int $isAvailable): ResultInterface
    {
        $this->_data[self::KEY_RESULT] = $isAvailable;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResult(): int
    {
        if (!empty($this->_data[self::KEY_OUT_OF_STOCK_ITEMS])) {
            return 0;
        }
        return $this->_data[self::KEY_RESULT];
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?ResultExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(ResultInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(ResultExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
