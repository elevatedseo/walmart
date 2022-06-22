<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceStatusInterface;
use Walmart\BopisInventoryCatalogApi\Api\InventoryAvailabilityServiceInterface;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupInterface;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupResultInterface;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupResultInterfaceFactory;
use Walmart\BopisInventorySourceApi\Model\Configuration;
use Walmart\BopisPreferredLocation\Model\GetSelectedLocation;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterfaceFactory;

class IsSkuSalableForInStorePickup implements IsSkuSalableForInStorePickupInterface
{
    /**
     * @var GetSelectedLocation
     */
    private GetSelectedLocation $getSelectedLocation;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    private IsSkuSalableForInStorePickupResultInterfaceFactory $isSkuSalableForInStorePickupResultFactory;

    private ItemRequestInterfaceFactory $inStoreRequestItemFactory;

    private InventoryAvailabilityRequestInterfaceFactory $inStoreRequestFactory;

    private InventoryAvailabilityServiceInterface $inventoryAvailabilityService;

    private SourceRepositoryInterface $sourceRepository;

    private array $messageDisplayedForSku;

    /**
     * @param GetSelectedLocation $getSelectedLocation
     * @param Configuration $configuration
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param ProductRepositoryInterface $productRepository
     * @param IsSkuSalableForInStorePickupResultInterfaceFactory $isSkuSalableForInStorePickupResultFactory
     * @param ItemRequestInterfaceFactory $inStoreRequestItemFactory
     * @param InventoryAvailabilityRequestInterfaceFactory $inStoreRequestFactory
     * @param InventoryAvailabilityServiceInterface $inventoryAvailabilityService
     */
    public function __construct(
        GetSelectedLocation $getSelectedLocation,
        Configuration $configuration,
        RequestInterface $request,
        ManagerInterface $messageManager,
        ProductRepositoryInterface $productRepository,
        SourceRepositoryInterface $sourceRepository,
        IsSkuSalableForInStorePickupResultInterfaceFactory $isSkuSalableForInStorePickupResultFactory,
        ItemRequestInterfaceFactory $inStoreRequestItemFactory,
        InventoryAvailabilityRequestInterfaceFactory $inStoreRequestFactory,
        InventoryAvailabilityServiceInterface $inventoryAvailabilityService
    ) {
        $this->getSelectedLocation = $getSelectedLocation;
        $this->configuration = $configuration;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->productRepository = $productRepository;
        $this->isSkuSalableForInStorePickupResultFactory = $isSkuSalableForInStorePickupResultFactory;
        $this->inStoreRequestItemFactory = $inStoreRequestItemFactory;
        $this->inStoreRequestFactory = $inStoreRequestFactory;
        $this->inventoryAvailabilityService = $inventoryAvailabilityService;
        $this->sourceRepository = $sourceRepository;
        $this->messageDisplayedForSku = [];
    }

    /**
     * @param IsProductSalableForRequestedQtyRequestInterface[] $skuRequests
     *
     * @return IsSkuSalableForInStorePickupResultInterface
     */
    public function execute(
        array $skuRequests
    ): IsSkuSalableForInStorePickupResultInterface {
        $isSalable = false;
        $isShipToStore = false;

        if ($this->getSelectedPickupLocation() === null) {
            $isSkuSalableForInStorePickupResult = $this->isSkuSalableForInStorePickupResultFactory->create();
            $isSkuSalableForInStorePickupResult->setIsSalable(true);
            $isSkuSalableForInStorePickupResult->setIsShipToStore(false);

            return $isSkuSalableForInStorePickupResult;
        }

        $inventoryAvailabilityResult = $this->getInventoryAvailabilityResult($skuRequests);

        if (isset($inventoryAvailabilityResult->getSourceList()[$this->getSelectedPickupLocation()])) {
            $selectedSource = $inventoryAvailabilityResult->getSourceList()[$this->getSelectedPickupLocation()];
            $isSalable = $selectedSource->getStatus()->getCode() === StockSourceStatusInterface::IN_STOCK;
            if (count($selectedSource->getInStockItems()) === 0) {
                $isSalable = false;
            }

            foreach ($selectedSource->getInStockItems() as $inStockItem) {
                foreach ($skuRequests as $skuRequest) {
                    if ($skuRequest->getSku() === $inStockItem->getSku()
                        && $inStockItem->getQty() < $skuRequest->getQty()
                    ) {
                        $isSalable = false;
                        break;
                    }
                }
            }
        }

        if ($this->canSendFromAnotherStore($isSalable)) {
            foreach ($inventoryAvailabilityResult->getSourceList() as $source) {
                if (count($source->getInStockItems()) === 0) {
                    continue;
                }

                $isSalable = true;
                $isShipToStore = true;
                $this->addNotificationForUser($skuRequests);
                break;
            }
        }

        $isSkuSalableForInStorePickupResult = $this->isSkuSalableForInStorePickupResultFactory->create();
        $isSkuSalableForInStorePickupResult->setIsSalable($isSalable);
        $isSkuSalableForInStorePickupResult->setIsShipToStore($isShipToStore);

        return $isSkuSalableForInStorePickupResult;
    }

    /**
     * @param bool $isSalable
     *
     * @return bool
     */
    private function canSendFromAnotherStore(bool $isSalable): bool
    {
        $selectedSource = null;
        if ($this->getSelectedPickupLocation()) {
            try {
                $selectedSource = $this->sourceRepository->get($this->getSelectedPickupLocation());
            } catch (NoSuchEntityException $exception) {
                // source not found
            }
        }

        return !$isSalable
               && $this->configuration->isShipToStoreEnabled()
               && ($selectedSource !== null && $selectedSource->getExtensionAttributes()->getAllowShipToStore());
    }

    /**
     * @param IsProductSalableForRequestedQtyRequestInterface[] $skuRequests
     *
     * @return void
     */
    private function addNotificationForUser(array $skuRequests): void
    {
        if (!$this->isAllowedAction()) {
            return;
        }

        foreach ($skuRequests as $skuRequest) {
            try {
                if(isset($this->messageDisplayedForSku[$skuRequest->getSku()]))
                {
                    continue;
                }

                $product = $this->productRepository->get($skuRequest->getSku());
                // @codingStandardsIgnoreStart
                $this->messageManager->addNoticeMessage(
                    __(
                        "The requested quantity for %1 is not available at the location you've selected. You can choose a different location otherwise we will ship your product to this location, which will delay your pickup by several days.",
                        $product->getName()
                    )
                );

                $this->messageDisplayedForSku[$skuRequest->getSku()] = true;

                // @codingStandardsIgnoreEnd
            } catch (NoSuchEntityException $exception) {
                continue;
            }
        }
    }

    /**
     * @return bool
     */
    private function isAllowedAction(): bool
    {


        if ($this->request->getModuleName() !== 'checkout') {
            return false;
        }

        if ($this->request->getActionName() === 'add') {
            return true;
        }

        return $this->request->getControllerName() === 'cart' && $this->request->getActionName() === 'index';
    }

    /**
     * @return string
     */
    private function getSelectedPickupLocation(): ?string
    {
        return $this->getSelectedLocation->execute();
    }

    /**
     * @param string $code
     *
     * @return SourceInterface
     * @throws NoSuchEntityException
     */
    private function getSource(string $code): SourceInterface
    {
        return $this->sourceRepository->get($code);
    }

    /**
     * @param array $skuRequests
     *
     * @return InventoryAvailabilityResultInterface
     */
    private function getInventoryAvailabilityResult(array $skuRequests): InventoryAvailabilityResultInterface
    {
        $inStoreRequestItems = [];
        foreach ($skuRequests as $request) {
            $inStoreRequestItems[] = $this->inStoreRequestItemFactory->create([
                'sku' => $request->getSku(),
                'qty' => $request->getQty(),
            ]);
        }

        $request = $this->inStoreRequestFactory->create(['items' => $inStoreRequestItems]);
        return $this->inventoryAvailabilityService->execute($request, false);
    }
}
