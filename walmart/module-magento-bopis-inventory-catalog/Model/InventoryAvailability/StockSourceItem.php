<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability;

use Magento\Framework\Model\AbstractExtensibleModel;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemExtensionInterface;

class StockSourceItem extends AbstractExtensibleModel implements StockSourceItemInterface
{
    /**
     * @inheritDoc
     */
    public function setSku(string $sku): StockSourceItemInterface
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
    public function setName(string $name): StockSourceItemInterface
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
    public function setQty(float $quantity): StockSourceItemInterface
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
    public function setImageUrl(string $imageUrl): StockSourceItemInterface
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
    public function setShipToStore(int $shipToStore): StockSourceItemInterface
    {
        $this->_data[self::KEY_SHIP_TO_STORE] = $shipToStore;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isShipToStore(): int
    {
        return $this->_data[self::KEY_SHIP_TO_STORE];
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): StockSourceItemInterface
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
     * @return StockSourceItemInterface
     */
    public function setErrorCode(string $errorCode): StockSourceItemInterface
    {
        $this->_data[self::KEY_ERROR_CODE] = $errorCode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?StockSourceItemExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(StockSourceItemInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }

        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(StockSourceItemExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
