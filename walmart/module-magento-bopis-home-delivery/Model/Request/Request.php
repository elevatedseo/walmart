<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Model\Request;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestExtensionInterface;

/**
 * Home Delivery Request body
 */
class Request extends AbstractExtensibleModel implements RequestInterface
{
    /**
     * @inheritDoc
     */
    public function setItems(array $items): void
    {
        $this->_data[self::KEY_ITEMS] = $items;
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array
    {
        return $this->_data[self::KEY_ITEMS];
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?RequestExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(RequestInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(RequestExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
