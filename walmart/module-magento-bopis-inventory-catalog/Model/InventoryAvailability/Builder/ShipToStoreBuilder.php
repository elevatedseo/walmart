<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\Builder;

use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Walmart\BopisInventoryCatalogApi\Api\BuilderInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventorySourceApi\Api\GetShipToStoreSourcesInterface;
use Walmart\BopisInventorySourceApi\Model\Configuration;

/**
 * Provides "ship_to_store" value
 */
class ShipToStoreBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;

    /**
     * @var GetShipToStoreSourcesInterface
     */
    private GetShipToStoreSourcesInterface $getShipToStoreSources;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;

    /**
     * @var GetProductSalableQtyInterface
     */
    private GetProductSalableQtyInterface $getProductSalableQty;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @param SubjectReader $subjectReader
     * @param GetShipToStoreSourcesInterface $getShipToStoreSources
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param Configuration $configuration
     */
    public function __construct(
        SubjectReader $subjectReader,
        GetShipToStoreSourcesInterface $getShipToStoreSources,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetProductSalableQtyInterface $getProductSalableQty,
        Configuration $configuration
    ) {
        $this->getShipToStoreSources = $getShipToStoreSources;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->configuration = $configuration;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        if (!$this->configuration->isShipToStoreEnabled()) {
            return [StockSourceItemInterface::KEY_SHIP_TO_STORE => 0];
        }

        $sourceCode = $this->subjectReader->readSourceCode($buildSubject);
        $sourceSearchResult = $this->getShipToStoreSources->execute([$sourceCode]);
        if ($sourceSearchResult->getTotalCount() === 0) {
            return [StockSourceItemInterface::KEY_SHIP_TO_STORE => 0];
        }

        $sku = $this->subjectReader->readSku($buildSubject);
        $stockId = $this->getStockIdForCurrentWebsite->execute();
        $productSalableQty = $this->getProductSalableQty->execute($sku, $stockId);
        $requestedQty = $this->subjectReader->readRequestedQty($buildSubject);

        return [StockSourceItemInterface::KEY_SHIP_TO_STORE => (int)($productSalableQty >= $requestedQty)];
    }
}
