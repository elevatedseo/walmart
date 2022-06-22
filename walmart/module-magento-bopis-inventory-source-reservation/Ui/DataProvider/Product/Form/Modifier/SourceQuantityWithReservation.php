<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Walmart\BopisInventorySourceReservation\Model\GetSourceQuantityWithReservationDataBySku;

/**
 * Product form modifier. Modify form stocks declaration to include source quantity with reservations.
 */
class SourceQuantityWithReservation extends AbstractModifier
{
    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType;

    /**
     * @var LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var IsSingleSourceModeInterface
     */
    private IsSingleSourceModeInterface $isSingleSourceMode;

    /**
     * @var GetSourceQuantityWithReservationDataBySku
     */
    private GetSourceQuantityWithReservationDataBySku $getSourceQuantityWithReservationDataBySku;

    /**
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param LocatorInterface                                     $locator
     * @param IsSingleSourceModeInterface                          $isSingleSourceMode
     * @param GetSourceQuantityWithReservationDataBySku            $getSourceQuantityWithReservationDataBySku
     */
    public function __construct(
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        LocatorInterface $locator,
        IsSingleSourceModeInterface $isSingleSourceMode,
        GetSourceQuantityWithReservationDataBySku $getSourceQuantityWithReservationDataBySku
    ) {
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->locator = $locator;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->getSourceQuantityWithReservationDataBySku = $getSourceQuantityWithReservationDataBySku;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();

        if ($this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false
            || null === $product->getId()
        ) {
            return $data;
        }

        $data[$product->getId()]['source_quantity_with_reservation'] =
            $this->getSourceQuantityWithReservationDataBySku->execute($product->getSku());

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
