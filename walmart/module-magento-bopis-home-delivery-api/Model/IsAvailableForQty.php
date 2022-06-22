<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDeliveryApi\Model;

use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;
use Walmart\BopisHomeDelivery\Model\GetHomeDeliveryFlagBySkus;
use Walmart\BopisHomeDeliveryApi\Api\Data\ItemRequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\IsAvailableForQtyInterface;
use Walmart\BopisInventoryCatalogApi\Api\BuilderInterface;
use Walmart\BopisInventoryCatalogApi\Model\ValidationErrorCode;
use Walmart\BopisInventorySourceApi\Api\GetWarehouseCodesBySkusInterface;
use Walmart\BopisInventorySourceApi\Model\Configuration;
use Walmart\BopisInventorySourceApi\Model\GetQtyBySourceAndSku;

/**
 * Is requested items as available for Home Delivery
 */
class IsAvailableForQty implements IsAvailableForQtyInterface
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var BuilderInterface
     */
    private BuilderInterface $oosItemBuilder;

    /**
     * @var ResultItemInterfaceFactory
     */
    private ResultItemInterfaceFactory $resultItemFactory;

    /**
     * @var ResultInterfaceFactory
     */
    private ResultInterfaceFactory $resultFactory;

    /**
     * @var GetHomeDeliveryFlagBySkus
     */
    private GetHomeDeliveryFlagBySkus $getHomeDeliveryFlagBySkus;

    /**
     * @var GetWarehouseCodesBySkusInterface
     */
    private GetWarehouseCodesBySkusInterface $getWarehouseCodesBySkus;

    /**
     * @var GetQtyBySourceAndSku
     */
    private GetQtyBySourceAndSku $getQtyBySourceAndSku;

    private LoggerInterface $logger;

    /**
     * @param Configuration $configuration
     * @param BuilderInterface $oosItemBuilder
     * @param ResultItemInterfaceFactory $resultItemFactory
     * @param ResultInterfaceFactory $resultFactory
     * @param GetHomeDeliveryFlagBySkus $getHomeDeliveryFlagBySkus
     * @param GetWarehouseCodesBySkusInterface $getWarehouseCodesBySkus
     * @param GetQtyBySourceAndSku $getQtyBySourceAndSku
     */
    public function __construct(
        Configuration $configuration,
        BuilderInterface $oosItemBuilder,
        ResultItemInterfaceFactory $resultItemFactory,
        ResultInterfaceFactory $resultFactory,
        GetHomeDeliveryFlagBySkus $getHomeDeliveryFlagBySkus,
        GetWarehouseCodesBySkusInterface $getWarehouseCodesBySkus,
        GetQtyBySourceAndSku $getQtyBySourceAndSku,
        LoggerInterface $logger
    ) {
        $this->configuration = $configuration;
        $this->oosItemBuilder = $oosItemBuilder;
        $this->resultItemFactory = $resultItemFactory;
        $this->resultFactory = $resultFactory;
        $this->getHomeDeliveryFlagBySkus = $getHomeDeliveryFlagBySkus;
        $this->getWarehouseCodesBySkus = $getWarehouseCodesBySkus;
        $this->getQtyBySourceAndSku = $getQtyBySourceAndSku;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(RequestInterface $request, bool $collectCartData = true): ResultInterface
    {
        $result = $this->resultFactory->create([
            'data' => [
                ResultInterface::KEY_RESULT => 0,
                ResultInterface::KEY_OUT_OF_STOCK_ITEMS => []
            ]
        ]);

        $skuToQtyMap = [];
        foreach ($request->getItems() as $item) {
            $skuToQtyMap[$item->getSku()] = $item->getQty();
        }
        $productToHomeDeliveryMap = $this->getHomeDeliveryFlagBySkus->execute(array_keys($skuToQtyMap));

        $sourceCodes = $this->getWarehouseCodesBySkus->execute(
            array_keys($skuToQtyMap),
            $this->configuration->isShipFromStoreEnabled()
        );

        if (empty($sourceCodes)) {
            return $result;
        }

        foreach ($request->getItems() as $item) {
            $availableQty = 0;
            foreach ($sourceCodes as $sourceCode) {
                $availableQty += $this->getQtyBySourceAndSku->execute($sourceCode, $item->getSku());
            }

            if ($item->getQty() > $availableQty
                || $this->hasDisabledHomeDelivery($item->getSku(), $productToHomeDeliveryMap)
            ) {
                try {
                    $stockItem = $this->buildItem($item->getSku(), $item->getQty());
                    $stockItem->setErrorCode(
                        $item->getQty() > $availableQty
                            ? ValidationErrorCode::ERROR_QTY
                            : ValidationErrorCode::ERROR_DELIVERY_METHOD_DISABLED
                    );
                    $result->addOutOfStockItem($stockItem);
                } catch (InputException $exception) {
                    $this->logger->info(
                        'Unable to get stock information',
                        [
                            'sku' => $item->getSku(),
                            'msg'=> $exception->getMessage()
                        ]
                    );
                }
            }
        }

        return $result->setResult((int) empty($result->getOutOfStockItems()));
    }

    /**
     * @param string $sku
     * @param array|null $productToHomeDeliveryMap
     *
     * @return bool
     */
    private function hasDisabledHomeDelivery(string $sku, array $productToHomeDeliveryMap): bool
    {
        return array_key_exists($sku, $productToHomeDeliveryMap) && $productToHomeDeliveryMap[$sku] === 0;
    }

    /**
     * @param string $sku
     * @param float $qty
     *
     * @return mixed
     * @throws InputException
     */
    private function buildItem(string $sku, float $qty): ResultItemInterface
    {
        $resultItemData = $this->oosItemBuilder->build(
            [
                'requested_qty' => $qty,
                'sku' => $sku,
            ]
        );

        return $this->resultItemFactory->create(['data' => $resultItemData])->setQty($qty);
    }
}
