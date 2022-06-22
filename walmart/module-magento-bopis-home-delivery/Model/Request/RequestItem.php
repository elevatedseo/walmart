<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Model\Request;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemExtensionInterface;

/**
 * Requested Item body
 */
class RequestItem extends AbstractExtensibleModel implements RequestItemInterface
{
    /**
     * @inheritdoc
     */
    public function getSku(): string
    {
        return $this->_data[self::KEY_SKU];
    }

    /**
     * @inheritdoc
     */
    public function getQty(): float
    {
        return (float) $this->_data[self::KEY_QTY];
    }

    /**
     * @inheritdoc
     */
    public function setSku(string $sku): void
    {
        $this->_data[self::KEY_SKU] = $sku;
    }

    /**
     * @inheritdoc
     */
    public function setQty(float $qty): void
    {
        $this->_data[self::KEY_QTY] = $qty;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?RequestItemExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(RequestItemInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(RequestItemExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
