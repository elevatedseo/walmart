<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Walmart\BopisBase\Model\Config as BopisBaseConfig;
use Walmart\BopisInventoryCatalogApi\Api\IsHomeDeliveryAvailableInterface;
use Walmart\BopisInventoryCatalogApi\Api\IsStorePickupAvailableInterface;

class ProductDeliveryMethods implements ArgumentInterface
{
    /**
     * @var IsHomeDeliveryAvailableInterface
     */
    private IsHomeDeliveryAvailableInterface $isHomeDeliveryAvailable;

    /**
     * @var IsStorePickupAvailableInterface
     */
    private IsStorePickupAvailableInterface $isStorePickupAvailable;

    /**
     * @var BopisBaseConfig
     */
    private BopisBaseConfig $bopisBaseConfig;

    /**
     * @param IsHomeDeliveryAvailableInterface $isHomeDeliveryAvailable
     * @param IsStorePickupAvailableInterface  $isStorePickupAvailable
     * @param BopisBaseConfig                  $bopisBaseConfig
     */
    public function __construct(
        IsHomeDeliveryAvailableInterface $isHomeDeliveryAvailable,
        IsStorePickupAvailableInterface $isStorePickupAvailable,
        BopisBaseConfig $bopisBaseConfig
    ) {
        $this->isHomeDeliveryAvailable = $isHomeDeliveryAvailable;
        $this->isStorePickupAvailable = $isStorePickupAvailable;
        $this->bopisBaseConfig = $bopisBaseConfig;
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function isHomeDeliveryMethodAvailable(ProductInterface $product): bool
    {
        return $this->isHomeDeliveryAvailable->execute($product->getSku());
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function isStorePickupMethodAvailable(ProductInterface $product): bool
    {
        return $this->isStorePickupAvailable->execute($product->getSku());
    }

    /**
     * @return bool
     */
    public function isStorePickupVisible(): bool
    {
        return $this->bopisBaseConfig->isInStorePickupEnabled();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->bopisBaseConfig->isEnabled();
    }
}
