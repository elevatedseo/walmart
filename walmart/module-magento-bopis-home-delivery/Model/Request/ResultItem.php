<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Model\Request;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemExtensionInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface;

/**
 * Result item body
 */
class ResultItem extends AbstractExtensibleModel implements ResultItemInterface
{
    /**
     * @inheritDoc
     */
    public function setSku(string $sku): ResultItemInterface
    {
        $this->_data[self::KEY_SKU] = $sku;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return $this->_data[self::KEY_SKU];
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): ResultItemInterface
    {
        $this->_data[self::KEY_NAME] = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->_data[self::KEY_NAME];
    }

    /**
     * @inheritDoc
     */
    public function setQty(float $quantity): ResultItemInterface
    {
        $this->_data[self::KEY_QTY] = $quantity;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQty(): float
    {
        return $this->_data[self::KEY_QTY];
    }

    /**
     * @inheritDoc
     */
    public function setImageUrl(string $imageUrl): ResultItemInterface
    {
        $this->_data[self::KEY_IMAGE_URL] = $imageUrl;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl(): ?string
    {
        return $this->_data[self::KEY_IMAGE_URL];
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): ResultItemInterface
    {
        $this->_data[self::KEY_OPTIONS] = $options;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->_data[self::KEY_OPTIONS];
    }

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->_data[self::KEY_ERROR_CODE] ?? null;
    }

    /**
     * @param string $errorCode
     *
     * @return ResultItemInterface
     */
    public function setErrorCode(string $errorCode): ResultItemInterface
    {
        $this->_data[self::KEY_ERROR_CODE] = $errorCode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?ResultItemExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(ResultItemInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(ResultItemExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
