<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestExtensionInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Store\Model\StoreManagerInterface;

class GetInventoryRequestFromQuote
{
    /**
     * @var InventoryRequestInterfaceFactory
     */
    private $inventoryRequestFactory;

    /**
     * @var InventoryRequestExtensionInterfaceFactory
     */
    private $inventoryRequestExtensionFactory;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressInterfaceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @param InventoryRequestInterfaceFactory $inventoryRequestFactory
     * @param InventoryRequestExtensionInterfaceFactory $inventoryRequestExtensionFactory
     * @param AddressInterfaceFactory $addressInterfaceFactory
     * @param StoreManagerInterface $storeManager
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     */
    public function __construct(
        InventoryRequestInterfaceFactory $inventoryRequestFactory,
        InventoryRequestExtensionInterfaceFactory $inventoryRequestExtensionFactory,
        AddressInterfaceFactory $addressInterfaceFactory,
        StoreManagerInterface $storeManager,
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
    ) {
        $this->inventoryRequestFactory = $inventoryRequestFactory;
        $this->inventoryRequestExtensionFactory = $inventoryRequestExtensionFactory;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
    }

    /**
     * @param CartInterface $quote
     * @param array $requestItems
     *
     * @return InventoryRequestInterface
     * @throws NoSuchEntityException
     */
    public function execute(CartInterface $quote, array $requestItems): InventoryRequestInterface
    {
        $store = $this->storeManager->getStore($quote->getStoreId());
        $stock = $this->stockByWebsiteIdResolver->execute((int)$store->getWebsiteId());

        $inventoryRequest = $this->inventoryRequestFactory->create(
            [
                'stockId' => $stock->getStockId(),
                'items' => $requestItems
            ]
        );

        $address = $this->getAddressFromQuote($quote);
        if ($address !== null) {
            $extensionAttributes = $this->inventoryRequestExtensionFactory->create();
            $extensionAttributes->setDestinationAddress($address);
            $inventoryRequest->setExtensionAttributes($extensionAttributes);
        }

        return $inventoryRequest;
    }

    /**
     * @param CartInterface $quote
     *
     * @return AddressInterface|null
     */
    private function getAddressFromQuote(CartInterface $quote): ?AddressInterface
    {
        /** @var Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress === null) {
            return null;
        }

        return $this->addressInterfaceFactory->create(
            [
                'country' => $shippingAddress->getCountryId(),
                'postcode' => $shippingAddress->getPostcode() ?? '',
                'street' => implode("\n", $shippingAddress->getStreet()),
                'region' => $shippingAddress->getRegion() ?? $shippingAddress->getRegionCode() ?? '',
                'city' => $shippingAddress->getCity() ?? ''
            ]
        );
    }
}
