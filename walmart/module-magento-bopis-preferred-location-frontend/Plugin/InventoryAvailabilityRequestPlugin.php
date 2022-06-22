<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\InventoryAvailabilityServiceInterface;

/**
 * Add products from customer quote to InventoryAvailability request
 */
class InventoryAvailabilityRequestPlugin
{
    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $quoteSession;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private ItemRequestInterfaceFactory $itemRequestFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SessionManagerInterface     $quoteSession
     * @param CartRepositoryInterface     $cartRepository
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param Config                      $config
     */
    public function __construct(
        SessionManagerInterface $quoteSession,
        CartRepositoryInterface $cartRepository,
        ItemRequestInterfaceFactory $itemRequestFactory,
        Config $config
    ) {
        $this->quoteSession = $quoteSession;
        $this->cartRepository = $cartRepository;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->config = $config;
    }

    /**
     * Add Cart products to the request
     *
     * @param InventoryAvailabilityServiceInterface $subject
     * @param InventoryAvailabilityRequestInterface $request
     * @param bool $collectCartData
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeExecute(
        InventoryAvailabilityServiceInterface $subject,
        InventoryAvailabilityRequestInterface $request,
        bool $collectCartData = true
    ): array {
        if (!$this->config->isEnabled()) {
            return [$request, $collectCartData];
        }

        if (!$collectCartData) {
            return [$request, $collectCartData];
        }

        $quote = $this->getQuote();
        if ($quote === null || !$quote->hasItems()) {
            return [$request, $collectCartData];
        }

        $missedProducts = [];
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getHasChildren()) { // BUNDLE
                foreach ($quoteItem->getChildren() as $child) {
                    $missedProducts[$child->getSku()] = $child->getQty();
                    foreach ($request->getItems() as $requestItem) {
                        if ($child->getSku() === $requestItem->getSku()) {
                            $requestItem->setQty($requestItem->getQty() + $child->getQty());
                            unset($missedProducts[$requestItem->getSku()]);
                        }
                    }
                }
                continue;
            }


            $missedProducts[$quoteItem->getSku()] = $quoteItem->getQty();
            foreach ($request->getItems() as $requestItem) {
                if ($quoteItem->getSku() === $requestItem->getSku()) {
                    $requestItem->setQty($requestItem->getQty() + $quoteItem->getQty());
                    unset($missedProducts[$requestItem->getSku()]);
                }
            }
        }

        $newRequestItems = [];
        foreach ($missedProducts as $sku => $qty) {
            $newRequestItems[] = $this->itemRequestFactory->create([
                'sku' => $sku,
                'qty' => $qty
            ]);
        }

        if ($newRequestItems) {
            $request->setItems(array_merge_recursive($request->getItems(), $newRequestItems));
        }

        return [$request, $collectCartData];
    }

    /**
     * @return CartInterface|null
     */
    private function getQuote(): ?CartInterface
    {
        $quoteId = $this->quoteSession->getQuoteId();
        try {
            return $this->cartRepository->get($quoteId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
