<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisHomeDelivery\Plugin;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterface;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\IsAvailableForQtyInterface;

/**
 * Add products from customer quote to HomeDelivery availability request
 *
 * @see \Walmart\BopisHomeDeliveryApi\Api\IsAvailableForQtyInterface
 */
class HomeDeliveryRequestPlugin
{
    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @var RequestItemInterfaceFactory
     */
    private RequestItemInterfaceFactory $itemRequestFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Session                     $checkoutSession
     * @param RequestItemInterfaceFactory $itemRequestFactory
     * @param Config                      $config
     */
    public function __construct(
        Session $checkoutSession,
        RequestItemInterfaceFactory $itemRequestFactory,
        Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->config = $config;
    }

    /**
     * Add Cart products to the request
     *
     * @param IsAvailableForQtyInterface $subject
     * @param RequestInterface $request
     * @param bool $collectCartData
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeExecute(
        IsAvailableForQtyInterface $subject,
        RequestInterface $request,
        bool $collectCartData = true
    ): array {
        if (!$this->config->isEnabled() || !$collectCartData) {
            return [$request, $collectCartData];
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote->hasItems()) {
            return [$request, $collectCartData];
        }

        $missedProducts = [];
        foreach ($quote->getItems() as $quoteItem) {
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
}
