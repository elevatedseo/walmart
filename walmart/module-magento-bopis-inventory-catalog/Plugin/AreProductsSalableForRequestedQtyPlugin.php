<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Magento\InventorySalesApi\Api\AreProductsSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyResultInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyResultInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory;
use Walmart\BopisApiConnector\Model\Config as BaseConfiguration;
use Walmart\BopisDeliverySelection\Model\GetSelectedDeliveryMethod;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultInterface;
use Walmart\BopisHomeDeliveryApi\Api\IsAvailableForQtyInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupInterface;

class AreProductsSalableForRequestedQtyPlugin
{
    /**
     * @var GetSelectedDeliveryMethod
     */
    private GetSelectedDeliveryMethod $getSelectedDeliveryMethod;

    /**
     * @var IsAvailableForQtyInterface
     */
    private IsAvailableForQtyInterface $homeDeliveryService;

    /**
     * @var RequestInterfaceFactory
     */
    private RequestInterfaceFactory $homeDeliveryRequestFactory;

    /**
     * @var RequestItemInterfaceFactory
     */
    private RequestItemInterfaceFactory $homeDeliveryRequestItemFactory;

    /**
     * @var IsProductSalableForRequestedQtyResultInterfaceFactory
     */
    private IsProductSalableForRequestedQtyResultInterfaceFactory $isSalableResultFactory;

    /**
     * @var ProductSalabilityErrorInterfaceFactory
     */
    private ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory;

    /**
     * @var BaseConfiguration
     */
    private BaseConfiguration $baseConfiguration;

    /**
     * @var IsSkuSalableForInStorePickupInterface
     */
    private IsSkuSalableForInStorePickupInterface $isSkuInStorePickupSalable;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ManageStockCondition
     */
    private ManageStockCondition $isManageStockDisabled;

    /**
     * @param GetSelectedDeliveryMethod $getSelectedDeliveryMethod
     * @param IsAvailableForQtyInterface $homeDeliveryService
     * @param RequestInterfaceFactory $homeDeliveryRequestFactory
     * @param RequestItemInterfaceFactory $homeDeliveryRequestItemFactory
     * @param IsProductSalableForRequestedQtyResultInterfaceFactory $isSalableResultFactory
     * @param ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory
     * @param BaseConfiguration $baseConfiguration
     * @param IsSkuSalableForInStorePickupInterface $isSkuInStorePickupSalable
     * @param RequestInterface $request
     * @param ManageStockCondition $isManageStockDisabled
     */
    public function __construct(
        GetSelectedDeliveryMethod $getSelectedDeliveryMethod,
        IsAvailableForQtyInterface $homeDeliveryService,
        RequestInterfaceFactory $homeDeliveryRequestFactory,
        RequestItemInterfaceFactory $homeDeliveryRequestItemFactory,
        IsProductSalableForRequestedQtyResultInterfaceFactory $isSalableResultFactory,
        ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory,
        BaseConfiguration $baseConfiguration,
        IsSkuSalableForInStorePickupInterface $isSkuInStorePickupSalable,
        RequestInterface $request,
        ManageStockCondition $isManageStockDisabled
    ) {
        $this->getSelectedDeliveryMethod = $getSelectedDeliveryMethod;
        $this->homeDeliveryService = $homeDeliveryService;
        $this->homeDeliveryRequestFactory = $homeDeliveryRequestFactory;
        $this->homeDeliveryRequestItemFactory = $homeDeliveryRequestItemFactory;
        $this->isSalableResultFactory = $isSalableResultFactory;
        $this->productSalabilityErrorFactory = $productSalabilityErrorFactory;
        $this->baseConfiguration = $baseConfiguration;
        $this->isSkuInStorePickupSalable = $isSkuInStorePickupSalable;
        $this->request = $request;
        $this->isManageStockDisabled = $isManageStockDisabled;
    }

    /**
     * @param AreProductsSalableForRequestedQtyInterface $subject
     * @param callable $proceed
     * @param IsProductSalableForRequestedQtyRequestInterface[] $skuRequests
     * @param int $stockId
     *
     * @return IsProductSalableForRequestedQtyResultInterface[]
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        AreProductsSalableForRequestedQtyInterface $subject,
        callable $proceed,
        array $skuRequests,
        int $stockId
    ): array {
        if (!$this->baseConfiguration->isEnabled()) {
            return $proceed($skuRequests, $stockId);
        }

        if ($this->getSelectedDeliveryMethod->execute() === GetSelectedDeliveryMethod::IN_STORE_PICKUP) {
            return $this->convertInStorePickupResult($skuRequests, $stockId);
        }

        $homeDeliveryRequest = [];
        foreach ($skuRequests as $requestItem) {
            $homeDeliveryRequest[] = $this->homeDeliveryRequestItemFactory->create([
                'data' => [
                    'sku' => $requestItem->getSku(),
                    'qty' => $requestItem->getQty(),
                ]
            ]);
        }

        $request = $this->homeDeliveryRequestFactory->create(['data' => [
            'items' => $homeDeliveryRequest
        ]]);

        $homeDeliveryResult = $this->homeDeliveryService->execute($request, false);
        return $this->convertHomeDeliveryResult($homeDeliveryResult, $skuRequests, $stockId);
    }

    /**
     * @param IsProductSalableForRequestedQtyRequestInterface[] $skuRequests
     * @param $stockId
     *
     * @return IsProductSalableForRequestedQtyResultInterface[]
     * @throws NoSuchEntityException
     */
    private function convertInStorePickupResult(
        array $skuRequests,
        $stockId
    ): array {
        $isSkuSalableResult = $this->isSkuInStorePickupSalable->execute($skuRequests);

        $data = [];
        foreach ($skuRequests as $request) {
            $isSalable = $isSkuSalableResult->isSalable();
            if ($this->isManageStockDisabled->execute($request->getSku(), $stockId)) {
                $isSalable = true;
            }

            $data[] = $this->isSalableResultFactory->create([
                'sku' => $request->getSku(),
                'stockId' => $stockId,
                'isSalable' => $isSalable,
                'errors' => $this->generateInStorePickupError($isSalable)
            ]);
        }

        return $data;
    }

    /**
     * @param ResultInterface $homeDeliveryResult
     * @param array $skuRequests
     * @param int $stockId
     *
     * @return IsProductSalableForRequestedQtyResultInterface[]
     * @throws NoSuchEntityException
     */
    private function convertHomeDeliveryResult(
        ResultInterface $homeDeliveryResult,
        array $skuRequests,
        int $stockId
    ): array {
        $isSalable = (bool) $homeDeliveryResult->getResult();

        $result = [];
        foreach ($skuRequests as $item) {
            if ($this->isManageStockDisabled->execute($item->getSku(), $stockId)) {
                $isSalable = true;
            }

            $result[] = $this->isSalableResultFactory->create([
                'sku' => $item->getSku(),
                'stockId' => $stockId,
                'isSalable' => $isSalable,
                'errors' => $this->generateHomeDeliveryError($isSalable)
            ]);
        }

        return $result;
    }

    /**
     * @param bool $isSalable
     * @return array
     */
    private function generateHomeDeliveryError(bool $isSalable): array
    {
        if (!$isSalable && !$this->isAddToCartAction()) {
            return [
                $this->productSalabilityErrorFactory->create([
                    'code' => 'not_salable_with_home_delivery',
                    'message' => __('The requested item is not available for Home Delivery')
                ])
            ];
        }

        return [];
    }

    /**
     * @param bool $isSalable
     * @return array
     */
    private function generateInStorePickupError(bool $isSalable): array
    {
        if (!$isSalable && !$this->isAddToCartAction()) {
            return [
                $this->productSalabilityErrorFactory->create([
                    'code' => 'not_salable_with_instore_pickup',
                    'message' => __('The requested item is not available for Instore Pickup')
                ])
            ];
        }

        return [];
    }

    /**
     * @return bool
     */
    private function isAddToCartAction(): bool
    {
        return $this->request->getModuleName() === 'checkout'
               && ($this->request->getActionName() === 'add' || $this->request->getActionName() === 'index');
    }
}
