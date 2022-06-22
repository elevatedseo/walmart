<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin\InventoryAvailability;

use Magento\Framework\Session\SessionManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Walmart\BopisInventoryCatalog\Model\InventoryAvailabilityService;
use Walmart\BopisInventorySourceApi\Model\Source\GetStorePickupSourceCodesBySkus;

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
     * @param SessionManager $quoteSession
     * @param CartRepositoryInterface $cartRepository
     * @param GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus
     */
    public function __construct(
        SessionManager $quoteSession,
        CartRepositoryInterface $cartRepository,
        GetStorePickupSourceCodesBySkus $getStorePickupSourceCodesBySkus
    ) {
        $this->quoteSession = $quoteSession;
        $this->cartRepository = $cartRepository;
        $this->getStorePickupSourceCodesBySkus = $getStorePickupSourceCodesBySkus;
    }

    /**
     *
     * @param InventoryAvailabilityService $subject
     * @param $result
     * @param string $sku
     *
     * @return mixed
     *@see \Walmart\BopisInventoryCatalog\Model\InventoryAvailabilityService::getProductRelatedSourceCodes
     */
    public function afterGetProductRelatedSourceCodes(
        InventoryAvailabilityService $subject,
        $result,
        string $sku
    ): array {
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

        if ($cartItem->getProductType() === 'bundle') {
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
