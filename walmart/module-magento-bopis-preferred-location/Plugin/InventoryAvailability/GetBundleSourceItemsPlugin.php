<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Plugin\InventoryAvailability;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\Source\GetStorePickupSourceCodesBySkus;
use Walmart\BopisPreferredLocationApi\Model\InventoryAvailabilityService;

/**
 * Get common Sources assigned to Bundle selections (Simple products)
 * If one of the Simple products assigned to Source "atlanta", but another
 * Simple products assigned to "atlanta" and "washington" - return "atlanta" only
 */
class GetBundleSourceItemsPlugin
{
    /**
     * @var SessionManager
     */
    private SessionManager $quoteSession;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @var GetStorePickupSourceCodesBySkus
     */
    private GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SessionManager                  $quoteSession
     * @param CartRepositoryInterface         $cartRepository
     * @param GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus
     * @param Config                          $config
     */
    public function __construct(
        SessionManager $quoteSession,
        CartRepositoryInterface $cartRepository,
        GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus,
        Config $config
    ) {
        $this->quoteSession = $quoteSession;
        $this->cartRepository = $cartRepository;
        $this->getStorePickupSourceCodesBySkus = $getStorePickupSourceCodesBySkus;
        $this->config = $config;
    }

    /**
     *
     * @see \Walmart\BopisPreferredLocationApi\Model\InventoryAvailabilityService::getProductRelatedSourceCodes
     * @param InventoryAvailabilityService $subject
     * @param $result
     * @param string $sku
     * @return mixed
     */
    public function afterGetProductRelatedSourceCodes(
        InventoryAvailabilityService $subject,
        $result,
        string $sku
    ): array {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!empty($result)) {
            return $result;
        }

        // handle products added to the cart only
        if (!$quote = $this->getQuote()) {
            return $result;
        }

        $cartItem = null;
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getSku() === $sku) {
                $cartItem = $quoteItem;
                break;
            }
        }

        if ($cartItem === null) {
            return $result;
        }

        if ($cartItem->getProductType() === Type::TYPE_BUNDLE) {
            $product = $cartItem->getProduct();
            $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
                    $product->getTypeInstance()->getOptionsIds($product),
                    $product
            );

            $skuToSourceMap = [];
            foreach ($selectionCollection->getItems() as $simpleItem) {
                $skuToSourceMap[$simpleItem->getSku()] = $this->getStorePickupSourceCodesBySkus->execute(
                    [$simpleItem->getSku()]
                );
            }

            return $this->getCommonSourcesOnly($skuToSourceMap);
        }

        return $result;
    }

    /**
     * @param array $sourcesMap
     * @return array
     */
    private function getCommonSourcesOnly(array $sourcesMap): array
    {
        $commonSources = array_values(current($sourcesMap));
        foreach ($sourcesMap as $sources) {
            $commonSources = array_intersect($commonSources, $sources);
        }

        return $commonSources ?? [];
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    private function getQuote()
    {
        $quoteId = $this->quoteSession->getQuoteId();
        try {
            return $this->cartRepository->get($quoteId);
        } catch (\Exception $exception) {}
        return null;
    }
}
