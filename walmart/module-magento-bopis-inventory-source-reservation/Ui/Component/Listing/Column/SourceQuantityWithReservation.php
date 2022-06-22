<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Walmart\BopisInventorySourceReservation\Model\GetSourceQuantityWithReservationDataBySku;
use Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Add grid column with source quantity with reservation data
 */
class SourceQuantityWithReservation extends Column
{
    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType;

    /**
     * @var IsSingleSourceModeInterface
     */
    private IsSingleSourceModeInterface $isSingleSourceMode;

    /**
     * @var GetSourceQuantityWithReservationDataBySku
     */
    private GetSourceQuantityWithReservationDataBySku $getSourceQuantityWithReservationDataBySku;

    /**
     * @param ContextInterface                                     $context
     * @param UiComponentFactory                                   $uiComponentFactory
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param IsSingleSourceModeInterface                          $isSingleSourceMode
     * @param GetSourceQuantityWithReservationDataBySku            $getSourceQuantityWithReservationDataBySku
     * @param array                                                $components
     * @param array                                                $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        IsSingleSourceModeInterface $isSingleSourceMode,
        GetSourceQuantityWithReservationDataBySku $getSourceQuantityWithReservationDataBySku,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->getSourceQuantityWithReservationDataBySku = $getSourceQuantityWithReservationDataBySku;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if ($dataSource['data']['totalRecords'] > 0) {
            foreach ($dataSource['data']['items'] as &$row) {
                $row['source_quantity_with_reservation'] =
                    $this->isSourceItemManagementAllowedForProductType->execute($row['type_id']) === true
                        ? $this->getSourceQuantityWithReservationDataBySku->execute($row['sku'])
                        : [];
            }
        }
        unset($row);

        return $dataSource;
    }
}
