<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Model;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventorySalesAdminUi\Model\GetStockSourceLinksBySourceCode;

class GetSourceQuantityWithReservationDataBySku
{
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private GetSourceItemsBySkuInterface $getSourceItemsBySku;

    /**
     * @var GetStockSourceLinksBySourceCode
     */
    private GetStockSourceLinksBySourceCode $getStockSourceLinksBySourceCode;

    /**
     * @var GetReservationsQuantityPerSourceInterface
     */
    private GetReservationsQuantityPerSourceInterface $getReservationsQuantityPerSource;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var StockRepositoryInterface
     */
    private StockRepositoryInterface $stockRepository;

    /**
     * @param GetSourceItemsBySkuInterface              $getSourceItemsBySku
     * @param GetStockSourceLinksBySourceCode           $getStockSourceLinksBySourceCode
     * @param GetReservationsQuantityPerSourceInterface $getReservationsQuantityPerSource
     * @param SourceRepositoryInterface                 $sourceRepository
     * @param StockRepositoryInterface                  $stockRepository
     */
    public function __construct(
        GetSourceItemsBySkuInterface $getSourceItemsBySku,
        GetStockSourceLinksBySourceCode $getStockSourceLinksBySourceCode,
        GetReservationsQuantityPerSourceInterface $getReservationsQuantityPerSource,
        SourceRepositoryInterface $sourceRepository,
        StockRepositoryInterface $stockRepository
    ) {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->getStockSourceLinksBySourceCode = $getStockSourceLinksBySourceCode;
        $this->getReservationsQuantityPerSource = $getReservationsQuantityPerSource;
        $this->sourceRepository = $sourceRepository;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @param string $sku
     *
     * @return array
     */
    public function execute(string $sku): array
    {
        $sourceData = [];
        $sourceItemsBySku = $this->getSourceItemsBySku->execute($sku);
        $allSourcesInfo = $this->getAllSourcesInfo();
        $allStocksInfo = $this->getAllStockInfo();
        foreach ($sourceItemsBySku as $sourceItem) {
            $sourceCode = $sourceItem->getSourceCode();
            $thresholdPerItem = (int) $sourceItem->getData('out_of_stock_threshold');

            if (!$allSourcesInfo[$sourceCode]['is_enabled']) {
                continue;
            }

            $stockSourceLinks = $this->getStockSourceLinksBySourceCode->execute($sourceCode);
            $reservation = $this->getReservationsQuantityPerSource->execute($sku, $sourceCode);
            $qtyAvailability = (int)$sourceItem->getStatus() === SourceItemInterface::STATUS_IN_STOCK
                ? $sourceItem->getQuantity() + $reservation
                : 0;

            foreach ($stockSourceLinks as $stockSourceLink) {
                $stockId = (int) $stockSourceLink->getStockId();
                $sourceData[$stockId]['stock_name'] = $allStocksInfo[$stockId]['name'];
                $sourceData[$stockId]['sources'][] = [
                    'source_code' => $allSourcesInfo[$sourceCode]['source_code'],
                    'source_name' => $allSourcesInfo[$sourceCode]['name'],
                    'qty' => $qtyAvailability,
                    'threshold_per_item' => $thresholdPerItem
                ];
            }
        }

        //reset array keys.
        return array_values($sourceData);
    }

    /**
     * Return sources information
     *
     * @return array
     */
    private function getAllSourcesInfo()
    {
        $data = [];
        $sources = $this->sourceRepository->getList()->getItems();
        foreach ($sources as $source) {
            $data[$source->getSourceCode()] = [
                'source_code' => $source->getSourceCode(),
                'is_enabled' => $source->isEnabled(),
                'name' => $source->getName()
            ];
        }

        return $data;
    }

    /**
     * Return stocks information
     *
     * @return array
     */
    private function getAllStockInfo(): array
    {
        $data = [];
        $stocks = $this->stockRepository->getList()->getItems();
        foreach ($stocks as $stock) {
            $data[$stock->getStockId()] = [
                'name' => $stock->getName()
            ];
        }

        return $data;
    }
}
