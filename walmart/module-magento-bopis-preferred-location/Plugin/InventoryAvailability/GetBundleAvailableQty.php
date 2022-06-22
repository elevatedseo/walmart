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
use Walmart\BopisInventorySourceApi\Model\GetQtyBySourceAndSku;
use Walmart\BopisPreferredLocationApi\Model\InventoryAvailabilityService;

/**
 * Calculate QTY for bundle product
 */
class GetBundleAvailableQty
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
     * @var GetQtyBySourceAndSku
     */
    private GetQtyBySourceAndSku $getQtyBySourceAndSku;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SessionManager          $quoteSession
     * @param CartRepositoryInterface $cartRepository
     * @param GetQtyBySourceAndSku    $getQtyBySourceAndSku
     * @param Config                  $config
     */
    public function __construct(
        SessionManager $quoteSession,
        CartRepositoryInterface $cartRepository,
        GetQtyBySourceAndSku $getQtyBySourceAndSku,
        Config $config
    ) {
        $this->quoteSession = $quoteSession;
        $this->cartRepository = $cartRepository;
        $this->getQtyBySourceAndSku = $getQtyBySourceAndSku;
        $this->config = $config;
    }

    /**
     * Calculate QTY for bundle product
     * Method calculates QTY for all Simple products and return MIN QTY of any Simple product
     *
     * @param InventoryAvailabilityService $subject
     * @param float $result
     * @param string $sourceCode
     * @param string $sku
     * @return float
     * @see \Walmart\BopisPreferredLocationApi\Model\InventoryAvailabilityService::getAvailableQty
     */
    public function afterGetAvailableQty(
        InventoryAvailabilityService $subject,
        float $result,
        string $sourceCode,
        string $sku
    ): float {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($result > 0) {
            return $result;
        }

        // plugin can handle cart items only
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

            // TODO refactor, remove MAP and get MIN value in this loop
            $skuToQtyMap = [];
            foreach ($selectionCollection->getItems() as $simpleItem) {
                $skuToQtyMap[$simpleItem->getSku()] = $this->getQtyBySourceAndSku->execute(
                    $sourceCode,
                    $simpleItem->getSku()
                );
            }

            // TODO MIN qty must be calculated in the loop above
            $minQty = current($skuToQtyMap);
            foreach ($skuToQtyMap as $sku => $qty) {
                $minQty = min($minQty, $qty);
            }

            return (float)$minQty;
        }

        return $result;
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    private function getQuote()
    {
        $quoteId = $this->quoteSession->getQuoteId();
        try {
            return $this->cartRepository->get($quoteId);
        } catch (\Exception $exception) {
        }
        return null;
    }
}
