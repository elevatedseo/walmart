<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin\InventoryAvailability;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\Data\CartInterface;
use Walmart\BopisInventoryCatalog\Model\InventoryAvailabilityService;
use Walmart\BopisInventorySourceApi\Model\GetQtyBySourceAndSku;

/**
 * Calculate QTY for bundle product
 */
class GetBundleAvailableQty
{
    /**
     * @var GetQtyBySourceAndSku
     */
    private GetQtyBySourceAndSku $getQtyBySourceAndSku;

    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @param GetQtyBySourceAndSku $getQtyBySourceAndSku
     * @param Session $checkoutSession
     */
    public function __construct(
        GetQtyBySourceAndSku $getQtyBySourceAndSku,
        Session $checkoutSession
    ) {
        $this->getQtyBySourceAndSku = $getQtyBySourceAndSku;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Calculate QTY for bundle product
     * Method calculates QTY for all Simple products and return MIN QTY of any Simple product
     *
     * @param InventoryAvailabilityService $subject
     * @param float $result
     * @param string $sourceCode
     * @param string $sku
     *
     * @return float
     * @see \Walmart\BopisInventoryCatalog\Model\InventoryAvailabilityService::getAvailableQty
     */
    public function afterGetAvailableQty(
        InventoryAvailabilityService $subject,
        float $result,
        string $sourceCode,
        string $sku
    ): float {
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

        if ($cartItem->getProductType() === 'bundle') {
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
     * @return CartInterface|null
     */
    private function getQuote(): ?CartInterface
    {
        try {
            return $this->checkoutSession->getQuote();
        } catch (Exception $exception) {
        }
        return null;
    }
}
