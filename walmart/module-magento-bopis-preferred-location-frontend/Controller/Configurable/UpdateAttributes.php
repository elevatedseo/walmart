<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Controller\Configurable;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventorySalesApi\Api\AreProductsSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Update Configurable attributes via Ajax POST request
 */
class UpdateAttributes implements ActionInterface, HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ConfigurableAttributeData
     */
    private ConfigurableAttributeData $configurableAttributeData;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Data
     */
    private Data $configurableHelper;

    /**
     * @var AreProductsSalableForRequestedQtyInterface
     */
    private AreProductsSalableForRequestedQtyInterface $areProductsSalableForRequestedQty;

    /**
     * @var IsProductSalableForRequestedQtyRequestInterfaceFactory
     */
    private IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory;

    /**
     * @var StockResolverInterface
     */
    private StockResolverInterface $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private GetStockItemConfigurationInterface $getStockItemConfiguration;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @param RequestInterface $request
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param ProductRepositoryInterface $productRepository
     * @param Data $configurableHelper
     * @param AreProductsSalableForRequestedQtyInterface $areProductsSalableForRequestedQty
     * @param IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory
     * @param StockResolverInterface $stockResolver
     * @param StoreManagerInterface $storeManager
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        RequestInterface $request,
        ConfigurableAttributeData $configurableAttributeData,
        ProductRepositoryInterface $productRepository,
        Data $configurableHelper,
        AreProductsSalableForRequestedQtyInterface $areProductsSalableForRequestedQty,
        IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        JsonFactory $resultJsonFactory
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configurableAttributeData = $configurableAttributeData;
        $this->productRepository = $productRepository;
        $this->configurableHelper = $configurableHelper;
        $this->areProductsSalableForRequestedQty = $areProductsSalableForRequestedQty;
        $this->isProductSalableForRequestedQtyRequestFactory = $isProductSalableForRequestedQtyRequestFactory;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
    }

    /**
     * Save Preferred Location via Ajax
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $result->setData(['attributes' => []]);

        if (!$productId = $this->request->getParam('productId')) {
            return $result;
        }

        try {
            $product = $this->productRepository->getById($productId);
            $options = $this->configurableHelper->getOptions($product, $this->getAllowProducts($product));
            $attributesData = $this->configurableAttributeData->getAttributesData($product, $options);

            $config = ['attributes' => $attributesData['attributes']];
            return $result->setData($config);
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * @param $product
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SkuIsNotAssignedToStockException
     */
    public function getAllowProducts($product): array
    {
        $skuToProductMap = [];
        $allProducts = $product->getTypeInstance()->getUsedProducts($product, null);
        foreach ($allProducts as $product) {
            $skuToProductMap[$product->getSku()] = $product;
        }

        $website = $this->storeManager->getWebsite();
        $stock = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $website->getCode());
        $skuRequests = [];
        foreach ($allProducts as $product) {
            $skuRequests[] = $this->isProductSalableForRequestedQtyRequestFactory->create(
                [
                    'sku' => $product->getSku(),
                    'qty' => $this->getRequestedQty($product, $stock->getStockId())
                ]
            );
        }

        // AreProductSalableForRequestedQty automatically detect delivery method and return calculations
        $products = [];
        $salableResult = $this->areProductsSalableForRequestedQty->execute($skuRequests, $stock->getStockId());
        foreach ($salableResult as $productResult) {
            if ($productResult->isSalable()) {
                $products[] = $skuToProductMap[$productResult->getSku()];
            }
        }

        return $products;
    }

    /**
     * @param Product $product
     * @param int $stockId
     * @return float
     * @throws LocalizedException
     * @throws SkuIsNotAssignedToStockException
     */
    private function getRequestedQty(Product $product, int $stockId): float
    {
        $stockItemConfiguration = $this->getStockItemConfiguration->execute((string)$product->getSku(), $stockId);
        return $stockItemConfiguration->getMinSaleQty();
    }
}
