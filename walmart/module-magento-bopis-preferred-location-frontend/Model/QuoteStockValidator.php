<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Model;

use Exception;
use Magento\CatalogInventory\Helper\Data;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup;
use Magento\InventorySales\Model\IsProductSalableCondition\ManageStockCondition;
use Magento\InventorySalesApi\Api\Data\IsProductSalableForRequestedQtyResultInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository\LoadHandler;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\Data\RequestItemInterfaceFactory;
use Walmart\BopisHomeDeliveryApi\Api\Data\ResultItemInterface;
use Walmart\BopisHomeDeliveryApi\Api\IsAvailableForQtyInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterfaceFactory;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\InventoryAvailabilityServiceInterface;
use Walmart\BopisInventoryCatalogApi\Model\ValidationErrorCode;
use Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source\PreferredMethodSource;
use Walmart\BopisPreferredLocation\Model\GetSelectedLocation;

class QuoteStockValidator implements QuoteValidationRuleInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var IsAvailableForQtyInterface
     */
    private IsAvailableForQtyInterface $isAvailableForQty;

    /**
     * @var InventoryAvailabilityServiceInterface
     */
    private InventoryAvailabilityServiceInterface $inventoryAvailabilityService;

    /**
     * @var RequestInterfaceFactory
     */
    private RequestInterfaceFactory $requestFactory;

    /**
     * @var InventoryAvailabilityRequestInterfaceFactory
     */
    private InventoryAvailabilityRequestInterfaceFactory $inventoryAvailabilityRequestFactory;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private ItemRequestInterfaceFactory $itemRequestFactory;

    /**
     * @var ManageStockCondition
     */
    private ManageStockCondition $isManageStockDisabled;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;

    /**
     * @var GetSelectedLocation
     */
    private GetSelectedLocation $getSelectedLocation;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var SaveHandler
     */
    private SaveHandler $quoteSaveHandler;


    /**
     * @var ValidationResultFactory
     */
    private ValidationResultFactory $validationResultFactory;

    /**
     * @param Config $config
     * @param IsAvailableForQtyInterface $isAvailableForQty
     * @param InventoryAvailabilityServiceInterface $inventoryAvailabilityService
     * @param InventoryAvailabilityRequestInterfaceFactory $inventoryAvailabilityRequestFactory
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param RequestInterfaceFactory $requestFactory
     * @param ManageStockCondition $isManageStockDisabled
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetSelectedLocation $getSelectedLocation
     * @param CustomerSession $customerSession
     * @param SaveHandler $quoteSaveHandler
     *
     */
    public function __construct(
        Config $config,
        IsAvailableForQtyInterface $isAvailableForQty,
        InventoryAvailabilityServiceInterface $inventoryAvailabilityService,
        InventoryAvailabilityRequestInterfaceFactory $inventoryAvailabilityRequestFactory,
        ItemRequestInterfaceFactory $itemRequestFactory,
        RequestInterfaceFactory $requestFactory,
        ManageStockCondition $isManageStockDisabled,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetSelectedLocation $getSelectedLocation,
        CustomerSession $customerSession,
        SaveHandler $quoteSaveHandler,
        ValidationResultFactory $validationResultFactory
    ) {
        $this->config = $config;
        $this->isAvailableForQty = $isAvailableForQty;
        $this->inventoryAvailabilityService = $inventoryAvailabilityService;
        $this->requestFactory = $requestFactory;
        $this->inventoryAvailabilityRequestFactory = $inventoryAvailabilityRequestFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->isManageStockDisabled = $isManageStockDisabled;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getSelectedLocation = $getSelectedLocation;
        $this->customerSession = $customerSession;
        $this->quoteSaveHandler = $quoteSaveHandler;
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function execute(
        CartInterface $quote
    ): CartInterface {
        if (!$this->config->isEnabled()) {
            return $quote;
        }

        if ($quote->getItems() === null) {
            return $quote;
        }

        // clean up errors messages added by default Magento validators
        $quote->removeMessageByText('qty', __('The requested qty is not available'));
        foreach ($quote->getItems() as $cartItem) {
            $cartItem->clearMessage();
        }

        if (count($quote->getErrors()) === 0) {
            $quote->setHasError(false);
        }

        $deliveryMethod = $this->getDeliveryMethod($quote);

        if($deliveryMethod === null)
            return $quote;

        $isInStorePickup = in_array(
            $deliveryMethod,
            [PreferredMethodSource::STORE_PICKUP_CODE, InStorePickup::DELIVERY_METHOD],
            true
        );

        if ($isInStorePickup) {
            $sourceCode = $this->getSelectedSourceCode($quote);
            if (!empty($sourceCode)) {
                return $this->validateInStorePickupStock($quote, $sourceCode);
            }
        } else {
            return $this->validateHomeDeliveryStock($quote);
        }

        return $quote;
    }

    /**
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    private function validateHomeDeliveryStock(CartInterface $quote): CartInterface
    {
        $quote->removeMessageByText('stock', __('Some items in the cart are not available for Home Delivery. Please review cart items before checkout.'));
        $items = $this->prepareItems($quote);
        // no need to validate quote when there are no matching items
        if (count($items) === 0) {
            return $quote;
        }

        $request = $this->requestFactory->create();
        $request->setItems($items);

        // validate the stock
        $data = $this->isAvailableForQty->execute($request, false);
        if (count($data->getOutOfStockItems()) === 0) {
            return $quote;
        }

        foreach ($data->getOutOfStockItems() as $outOfStockItem) {
            // if any of the items marked as out of stock - display error message and block checkout
            foreach ($quote->getItems() as $cartItem) {
                if ($cartItem->getHasChildren()) {
                    foreach($cartItem->getChildren() as $child) {
                        if ($child->getSku() === $outOfStockItem->getSku()) {
                            $cartItem->clearMessage();
                            $cartItem->addErrorInfo(
                                'cataloginventory',
                                Data::ERROR_QTY,
                                $this->getHomeDeliveryErrorMessage($outOfStockItem)
                            );
                        }

                    }
                    continue;
                }

                if ($cartItem->getSku() === $outOfStockItem->getSku()) {
                    $cartItem->clearMessage();
                    $cartItem->addErrorInfo(
                        'cataloginventory',
                        Data::ERROR_QTY,
                        $this->getHomeDeliveryErrorMessage($outOfStockItem)
                    );
                }
            }
        }

        $quote->addErrorInfo(
            'stock',
            'cataloginventory',
            Data::ERROR_QTY,
            __('Some items in the cart are not available for Home Delivery. Please review cart items before checkout.')
        );

        return $quote;
    }

    /**
     * @param CartInterface $quote
     * @param string $sourceCode
     *
     * @return CartInterface
     */
    private function validateInStorePickupStock(CartInterface $quote, string $sourceCode): CartInterface
    {
        $quote->removeMessageByText('stock', __('Some items in the cart are not available for Store Pickup. Please review cart items before checkout.'));
        $items = $this->prepareItems($quote);
        // no need to validate quote when there are no matching items
        if (count($items) === 0) {
            return $quote;
        }

        $request = $this->inventoryAvailabilityRequestFactory->create([
            'items' => $items
        ]);

        // validate the stock
        $data = $this->inventoryAvailabilityService->execute($request, false);
        foreach ($data->getSourceList() as $source) {
            if ($source->getSourceCode() !== $sourceCode) {
                continue;
            }

            if (count($source->getOutOfStockItems()) === 0) {
                return $quote;
            }

            foreach ($source->getOutOfStockItems() as $outOfStockItem) {
                foreach ($quote->getItems() as $cartItem) {
                    if ( $cartItem->getHasChildren()) {
                        foreach($cartItem->getChildren() as $child) {
                            if ($child->getSku() === $outOfStockItem->getSku()) {
                                $cartItem->clearMessage();
                                $cartItem->addErrorInfo(
                                    'cataloginventory',
                                    Data::ERROR_QTY,
                                    $this->getInstorePickupErrorMessage($outOfStockItem)
                                );
                            }
                        }
                        continue;
                    }

                    if ($cartItem->getSku() === $outOfStockItem->getSku()) {
                        $cartItem->clearMessage();
                        $cartItem->addErrorInfo(
                            'cataloginventory',
                            Data::ERROR_QTY,
                            $this->getInstorePickupErrorMessage($outOfStockItem)
                        );
                    }
                }
            }

            $quote->addErrorInfo(
                'stock',
                'cataloginventory',
                Data::ERROR_QTY,
                __('Some items in the cart are not available for Store Pickup. Please review cart items before checkout.')
            );
            $quote->setHasError(true);

        }

        return $quote;
    }

    /**
     * @param CartInterface $quote
     *
     * @return string|null
     */
    private function getDeliveryMethod(CartInterface $quote): ?string
    {
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress) {
            return null;
        }

        return $shippingAddress->getShippingMethod();
    }

    /**
     * @param CartInterface $quote
     *
     * @return string|null
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     */
    private function getSelectedSourceCode(CartInterface $quote): ?string
    {
        $shippingAddress = $quote->getShippingAddress();
        $sourceCode = $shippingAddress->getExtensionAttributes()->getPickupLocationCode();

        if (!empty($sourceCode)) {
            return $sourceCode;
        }

        return $this->getSelectedLocation->execute() ?: $this->customerSession->getPreferredLocation();
    }

    /**
     * @param CartInterface $quote
     *
     * @return array
     */
    private function prepareItems(CartInterface $quote): array
    {
        $items = [];
        $stockId = $this->getStockIdForCurrentWebsite->execute();

        foreach ($quote->getItems() as $cartItem) {
            if ($this->isManageStockDisabled->execute($cartItem->getSku(), $stockId)) {
                continue;
            }

            if ($cartItem->getHasChildren()) {
                foreach($cartItem->getChildren() as $child) {
                    $item = $this->itemRequestFactory->create([
                        'sku' => $child->getSku(),
                        'qty' => $child->getQty()
                    ]);
                    $items[] = $item;
                }
            } else {
                $item = $this->itemRequestFactory->create([
                    'sku' => $cartItem->getSku(),
                    'qty' => $cartItem->getQty()
                ]);
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param ResultItemInterface $item
     *
     * @return Phrase
     */
    private function getHomeDeliveryErrorMessage(ResultItemInterface $item): Phrase
    {
        $errorCode = $item->getErrorCode();
        if (!$errorCode || $errorCode === ValidationErrorCode::ERROR_DELIVERY_METHOD_DISABLED) {
            return __('Product is not available for Home Delivery');
        }

        if ($errorCode === ValidationErrorCode::ERROR_QTY) {
            return __('The requested qty is not available');
        }

        return __('Product is not available for Home Delivery');
    }

    /**
     * @param StockSourceItemInterface $item
     *
     * @return Phrase
     */
    private function getInstorePickupErrorMessage(StockSourceItemInterface $item): Phrase
    {
        $errorCode = $item->getErrorCode();
        if (!$errorCode || $errorCode === ValidationErrorCode::ERROR_DELIVERY_METHOD_DISABLED) {
            return __('Product is not available for Store Pickup');
        }

        if ($errorCode === ValidationErrorCode::ERROR_QTY && $item->isShipToStore() == 0) {
            return __('Product is not available for Store Pickup');
        }

        return __('The requested qty is not available');
    }

    public function validate(Quote $quote): array
    {
        $this->execute($quote);

        return [$this->validationResultFactory->create(['errors' => $quote->getErrors()])];
    }
}
