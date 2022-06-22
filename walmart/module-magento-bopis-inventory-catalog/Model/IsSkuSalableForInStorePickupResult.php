<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Magento\Framework\DataObject;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupResultInterface;

class IsSkuSalableForInStorePickupResult extends DataObject implements IsSkuSalableForInStorePickupResultInterface
{
    /**
     * @inheritDoc
     */
    public function isSalable(): bool
    {
        return (bool)$this->getData(self::IS_SALABLE);
    }

    /**
     * @param bool $isSalable
     *
     * @return $this
     */
    public function setIsSalable(bool $isSalable): self
    {
        $this->setData(self::IS_SALABLE, $isSalable);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isShipToStore(): bool
    {
        return (bool)$this->getData(self::IS_SHIP_TO_STORE);
    }

    /**
     * @param bool $isShipToStore
     *
     * @return $this
     */
    public function setIsShipToStore(bool $isShipToStore): self
    {
        $this->setData(self::IS_SHIP_TO_STORE, $isShipToStore);

        return $this;
    }
}
