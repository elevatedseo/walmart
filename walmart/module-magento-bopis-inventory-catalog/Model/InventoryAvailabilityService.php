<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Walmart\BopisInventoryCatalog\Model\IsProductAvailableCondition\AllowInStorePickup;
use Walmart\BopisInventoryCatalogApi\Api\BuilderInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\InventoryAvailabilityServiceInterface;
use Walmart\BopisInventoryCatalogApi\Model\ValidationErrorCode;
use Walmart\BopisInventorySourceApi\Model\GetQtyBySourceAndSku;
use Walmart\BopisInventorySourceApi\Model\Source\GetStorePickupSourceCodesBySkus;

/**
 * Class return information about products inventory availability in all related stock sources
 */
class InventoryAvailabilityService implements InventoryAvailabilityServiceInterface
{
    /**
     * @var InventoryAvailabilityResultInterfaceFactory
     */
    private InventoryAvailabilityResultInterfaceFactory $inventoryAvailabilityResultInterfaceFactory;

    /**
     * @var StockSourceInterfaceFactory
     */
    private StockSourceInterfaceFactory $stockSourceInterfaceFactory;

    /**
     * @var BuilderInterface
     */
    private BuilderInterface $stockSourceItemBuilder;

    /**
     * @var StockSourceItemInterfaceFactory
     */
    private StockSourceItemInterfaceFactory $stockSourceItemFactory;

    /**
     * @var GetStorePickupSourceCodesBySkus
     */
    private GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus;

    /**
     * @var GetQtyBySourceAndSku
     */
    private GetQtyBySourceAndSku $getQtyBySourceAndSku;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority;

    /**
     * @var AllowInStorePickup
     */
    private IsProductAvailableCondition\AllowInStorePickup $allowInStorePickup;

    private ManageStockCondition $isManageStockDisabled;

    /**
     * @param InventoryAvailabilityResultInterfaceFactory $inventoryAvailabilityResultInterfaceFactory
     * @param StockSourceInterfaceFactory $stockSourceInterfaceFactory
     * @param BuilderInterface $stockSourceItemBuilder
     * @param StockSourceItemInterfaceFactory $stockSourceItemFactory
     * @param GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus
     * @param GetQtyBySourceAndSku $getQtyBySourceAndSku
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority
     * @param AllowInStorePickup $allowInStorePickup
     */
    public function __construct(
        InventoryAvailabilityResultInterfaceFactory $inventoryAvailabilityResultInterfaceFactory,
        StockSourceInterfaceFactory $stockSourceInterfaceFactory,
        BuilderInterface $stockSourceItemBuilder,
        StockSourceItemInterfaceFactory $stockSourceItemFactory,
        GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus,
        GetQtyBySourceAndSku $getQtyBySourceAndSku,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        AllowInStorePickup $allowInStorePickup,
        ManageStockCondition $isManageStockDisabled
    ) {
        $this->inventoryAvailabilityResultInterfaceFactory = $inventoryAvailabilityResultInterfaceFactory;
        $this->stockSourceInterfaceFactory = $stockSourceInterfaceFactory;
        $this->stockSourceItemBuilder = $stockSourceItemBuilder;
        $this->stockSourceItemFactory = $stockSourceItemFactory;
        $this->getStorePickupSourceCodesBySkus = $getStorePickupSourceCodesBySkus;
        $this->getQtyBySourceAndSku = $getQtyBySourceAndSku;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->allowInStorePickup = $allowInStorePickup;
        $this->isManageStockDisabled = $isManageStockDisabled;
    }

    /**
     * @inheritDoc
     */
    public function execute(
        InventoryAvailabilityRequestInterface $request,
        bool $collectCartData = true
    ): InventoryAvailabilityResultInterface {
        $skuToQtyMap = [];
        foreach ($request->getItems() as $item) {
            $skuToQtyMap[$item->getSku()] = $item->getQty();
        }

        $skusBySource = $this->aggregateData($request);
        $stockId = $this->getStockIdForCurrentWebsite->execute();

        $sourceListResult = [];
        foreach ($skusBySource as $sourceCode => $skus) {
            $sourceCode = (string) $sourceCode; // PHP is just to dump to NOT convert string to int with array keys
            $stockSourceRaw = [];
            foreach ($skus as $sku => $sourceItem) {
                if ($this->isManageStockDisabled->execute($sku, $stockId)) {
                    continue;
                }

                $requestedQty = $skuToQtyMap[$sku];
                try {
                    $stockSourceItemDataResult = $this->stockSourceItemBuilder->build([
                        'requested_qty' => $requestedQty,
                        'source_code' => $sourceCode,
                        'sku' => $sku,
                    ]);
                } catch (InputException $e) {
                    $stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK][$sku] =
                        $this->stockSourceItemFactory->create(
                            [
                                'data' => [
                                    StockSourceItemInterface::KEY_SKU => $sku,
                                    StockSourceItemInterface::KEY_QTY => 0
                                ]
                            ]
                        );
                    continue;
                }

                /** @var StockSourceItemInterface $stockSourceItem */
                $stockSourceItem = $this->stockSourceItemFactory->create(['data' => $stockSourceItemDataResult]);
                $stockSourceItem->setQty($requestedQty);

                if ($sourceItem === null || !$this->allowInStorePickup->execute([$sku], [])) {
                    $stockSourceItem->setErrorCode(ValidationErrorCode::ERROR_DELIVERY_METHOD_DISABLED);
                    $stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK][$sku] = $stockSourceItem;
                    continue;
                }

                $availableQty = $this->getAvailableQty($sourceCode, $sku);
                if ($availableQty >= $requestedQty || $stockSourceItem->isShipToStore()) {
                    $stockSourceRaw[$sourceCode][StockSourceInterface::IN_STOCK][$sku] =
                        $stockSourceItem->setQty(min($availableQty, $requestedQty));
                } else {
                    $stockSourceItem->setErrorCode(ValidationErrorCode::ERROR_QTY);
                    $stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK][$sku] =
                        $stockSourceItem->setQty(min($availableQty, $requestedQty));
                }
            }

            if (!isset($stockSourceRaw[$sourceCode][StockSourceInterface::IN_STOCK])) {
                $stockSourceRaw[$sourceCode][StockSourceInterface::IN_STOCK] = [];
            }

            if (!isset($stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK])) {
                $stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK] = [];
            }

            $stockSourceRaw = $this->stockSourceInterfaceFactory->create(
                [
                    'sourceCode' => $sourceCode,
                    'inStockItems' => $stockSourceRaw[$sourceCode][StockSourceInterface::IN_STOCK],
                    'outOfStockItems' => $stockSourceRaw[$sourceCode][StockSourceInterface::OUT_OF_STOCK],
                ]
            );
            $sourceListResult[$sourceCode] = $stockSourceRaw;
        }

        return $this->inventoryAvailabilityResultInterfaceFactory->create(
            [
                'sourceList' => $sourceListResult,
            ]
        );
    }

    /**
     * Get Available Qty in Source with reservations
     *
     * @param string $sourceCode
     * @param string $sku
     * @return float
     */
    public function getAvailableQty(string $sourceCode, string $sku): float
    {
        return $this->getQtyBySourceAndSku->execute($sourceCode, $sku);
    }

    /**
     * Map Sources to Skus
     *
     * @param InventoryAvailabilityRequestInterface $request
     * @return array
     */
    private function aggregateData(InventoryAvailabilityRequestInterface $request): array
    {
        $result = [];
        $allSources = $this->getActiveLocations();
        foreach ($request->getItems() as $requestItem) {
            $productSourceCodes = $this->getProductRelatedSourceCodes($requestItem->getSku());
            foreach ($productSourceCodes as $sourceCode) {
                $result[$sourceCode][$requestItem->getSku()] = true;
            }

            $notAssignedSources = array_diff($allSources, $productSourceCodes);
            foreach ($notAssignedSources as $sourceCode) {
                $result[$sourceCode][$requestItem->getSku()] = null;
            }
        }

        // Add not assigned SKUs to available Sources
        foreach ($result as $sourceCode => $skus) {
            foreach ($request->getItems() as $requestItem) {
                if (!isset($skus[$requestItem->getSku()])) {
                    $result[(string) $sourceCode][$requestItem->getSku()] = null;
                }
            }
        }

        return $result;
    }

    /**
     * Get Source codes related to SKU - (SKU is assigned to provided Sources)
     *
     * @param string $sku
     * @return string[]
     */
    public function getProductRelatedSourceCodes(string $sku): array
    {
        return $this->getStorePickupSourceCodesBySkus->execute([$sku]);
    }

    /**
     * @return string[]
     */
    private function getActiveLocations(): array
    {
        try {
            $stockId = $this->getStockIdForCurrentWebsite->execute();
            $stockSources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

            $sources = array_filter(
                $stockSources,
                static function (SourceInterface $source): bool {
                    return $source->getExtensionAttributes()
                           && (bool)$source->isEnabled()
                           && (bool)$source->getExtensionAttributes()->getIsPickupLocationActive();
                }
            );

            return array_map(static function ($source) {
                return $source->getSourceCode();
            }, $sources);
        } catch (Exception $exception) {
            return [];
        }
    }
}
