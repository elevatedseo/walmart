<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyRequestInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisInventoryCatalogApi\Api\IsSkuSalableForInStorePickupInterface;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterfaceFactory;

class SetShipToStoreOrder implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var IsSkuSalableForInStorePickupInterface
     */
    private IsSkuSalableForInStorePickupInterface $isSkuSalableForInStorePickup;

    /**
     * @var IsProductSalableForRequestedQtyRequestInterfaceFactory
     */
    private IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param IsSkuSalableForInStorePickupInterface $isSkuSalableForInStorePickup
     * @param IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        IsSkuSalableForInStorePickupInterface $isSkuSalableForInStorePickup,
        IsProductSalableForRequestedQtyRequestInterfaceFactory $isProductSalableForRequestedQtyRequestFactory,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->isSkuSalableForInStorePickup = $isSkuSalableForInStorePickup;
        $this->isProductSalableForRequestedQtyRequestFactory = $isProductSalableForRequestedQtyRequestFactory;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Set extension attributes for ShipToStore order items and Order
     *
     * @param Observer $observer
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return;
        }

        $pickupLocationCode = $quote->getShippingAddress()->getExtensionAttributes()->getPickupLocationCode();
        if (empty($pickupLocationCode)) {
            return;
        }

        $isShipToStore = false;

        foreach ($order->getAllItems() as $item) {
            try {
                // Skip composite products and virtual products
                if(!empty($item->getChildrenItems()) || $item->getIsVirtual())
                {
                    continue;
                }

                $isSkuSalableResult = $this->isSkuSalableForInStorePickup->execute(
                    [
                        $this->isProductSalableForRequestedQtyRequestFactory->create([
                            'qty' => $item->getQtyOrdered(),
                            'sku' => $item->getSku()
                        ])
                    ]
                );

                if ($isSkuSalableResult->isShipToStore()) {
                    $item->getExtensionAttributes()->setWmtShipToStore(1);
                    $observer->getEvent()->getOrder()->getExtensionAttributes()
                        ->setWmtStsEmailStatus(IsShipToStore::EMAIL_STATUS_PENDING);

                    if ($isShipToStore) {
                        continue;
                    }
                    $isShipToStore = $isSkuSalableResult->isShipToStore();
                }
            } catch (NoSuchEntityException $exception) {
                $this->logger->error(
                    __('Can\'t identify Ship To Store items for Order #%1', $order->getIncrementId()),
                    ['exception' => $exception->getMessage()]
                );
            }
        }

        $order->getExtensionAttributes()->setWmtIsShipToStore((int)$isShipToStore);
    }
}
