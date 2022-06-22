<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\InventoryInStorePickupApi\Model\GetPickupLocationInterface;
use Magento\InventoryInStorePickupQuote\Model\Address\SetAddressPickupLocation;
use Magento\InventoryInStorePickupQuote\Model\GetWebsiteCodeByStoreId;
use Magento\InventoryInStorePickupQuote\Model\ResourceModel\DeleteQuoteAddressPickupLocation;
use Magento\InventoryInStorePickupQuote\Model\ToQuoteAddress;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;

/**
 * Class is responsible for updating quote/quote address after changing location/delivery method
 */
class QuoteAddressManagement
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var GuestCartManagementInterface
     */
    private GuestCartManagementInterface $guestCartManagement;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;

    /**
     * @var CartManagementInterface
     */
    private CartManagementInterface $cartManagement;

    /**
     * @var SetAddressPickupLocation
     */
    private SetAddressPickupLocation $setAddressPickupLocation;

    /**
     * @var DeleteQuoteAddressPickupLocation
     */
    private DeleteQuoteAddressPickupLocation $deleteQuoteAddressPickupLocation;

    /**
     * @var GetPickupLocationInterface
     */
    private GetPickupLocationInterface $getPickupLocation;

    /**
     * @var GetWebsiteCodeByStoreId
     */
    private GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId;

    /**
     * @var ToQuoteAddress
     */
    private ToQuoteAddress $addressConverter;

    /**
     * @param CustomerSession $customerSession
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param GuestCartManagementInterface $guestCartManagement
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param CartManagementInterface $cartManagement
     * @param SetAddressPickupLocation $setAddressPickupLocation
     * @param DeleteQuoteAddressPickupLocation $deleteQuoteAddressPickupLocation
     * @param GetPickupLocationInterface $getPickupLocation
     * @param GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId
     * @param ToQuoteAddress $addressConverter
     */
    public function __construct(
        CustomerSession $customerSession,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        GuestCartManagementInterface $guestCartManagement,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CartManagementInterface $cartManagement,
        SetAddressPickupLocation $setAddressPickupLocation,
        DeleteQuoteAddressPickupLocation $deleteQuoteAddressPickupLocation,
        GetPickupLocationInterface $getPickupLocation,
        GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId,
        ToQuoteAddress $addressConverter
    ) {
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->guestCartManagement = $guestCartManagement;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->cartManagement = $cartManagement;
        $this->setAddressPickupLocation = $setAddressPickupLocation;
        $this->deleteQuoteAddressPickupLocation = $deleteQuoteAddressPickupLocation;
        $this->getPickupLocation = $getPickupLocation;
        $this->getWebsiteCodeByStoreId = $getWebsiteCodeByStoreId;
        $this->addressConverter = $addressConverter;
    }

    /**
     * Get Quote or Create Quote
     */
    public function getOrCreateQuote()
    {
        try {
            $quoteId = $this->checkoutSession->getQuoteId();
            $quote = $this->quoteRepository->getActive($quoteId);
        } catch (NoSuchEntityException $e) {
            if ($this->customerSession->getCustomerId()) {
                $quoteId = $this->cartManagement->createEmptyCartForCustomer($this->customerSession->getCustomerId());
            } else {
                $quoteMaskId = $this->guestCartManagement->createEmptyCart();
                $quoteId = $this->maskedQuoteIdToQuoteId->execute($quoteMaskId);
            }
            $quote = $this->quoteRepository->get($quoteId);
            $this->checkoutSession->setQuoteId($quoteId);
        }

        return $quote;
    }

    /**
     * Set shipping address data of selected source
     *
     * @param CartInterface $quote
     * @param array $quoteDeliveryMethodData
     *
     * @throws InputException
     */
    public function deliveryMethodChange(CartInterface $quote, array $quoteDeliveryMethodData): void
    {
        $extensionAttributes = $quote->getExtensionAttributes();

        if (!$quote->isVirtual() && $extensionAttributes && $extensionAttributes->getShippingAssignments()) {
            $shippingAssignments = $extensionAttributes->getShippingAssignments();

            if (count($shippingAssignments) > 1) {
                throw new InputException(__('Only 1 shipping assignment can be set'));
            }
            $shippingAssignment = $shippingAssignments[0];
            $shippingAddress = $shippingAssignment->getShipping()->getAddress();
            $shippingAssignment->getShipping()->setMethod($quoteDeliveryMethodData['shipping_method'] ?? '');

            if ($quoteDeliveryMethodData['delivery_method'] === 'home') {
                $this->deleteQuoteAddressPickupLocation->execute((int)$shippingAddress->getId());
                $shippingAddress->getExtensionAttributes()->setPickupLocationCode('');
            }

            if ($quoteDeliveryMethodData['delivery_method'] === 'instore_pickup') {
                $shippingAssignment->getShipping()->setMethod($quoteDeliveryMethodData['delivery_method']);
            }

            $quote->getShippingAddress()->setShippingMethod($quoteDeliveryMethodData['shipping_method'] ?? '');
        }
    }

    /**
     * Update location quote data
     *
     * @param CartInterface $quote
     * @param array $quoteDeliveryMethodData
     */
    public function locationChange(CartInterface $quote, array $quoteDeliveryMethodData): void
    {
        $shippingAddress = $quote->getShippingAddress();

        try {
            if (isset($quoteDeliveryMethodData['pickup_location_code'], $quoteDeliveryMethodData['delivery_method'])
                && $quoteDeliveryMethodData['delivery_method'] === 'instore_pickup'
            ) {
                $shippingAddress->setShippingMethod($quoteDeliveryMethodData['delivery_method']);
                $this->setAddressPickupLocation->execute(
                    $shippingAddress,
                    $quoteDeliveryMethodData['pickup_location_code']
                );

                $pickupLocation = $this->getPickupLocation($quote, $shippingAddress);
                $address = $this->addressConverter->convert($pickupLocation, $shippingAddress);
                $quote->setShippingAddress($address);
            }
        } catch (NoSuchEntityException $e) {
            //location is not selected, do nothing
        }
    }

    /**
     * Get Pickup Location entity, assigned to Shipping Address.
     *
     * @param CartInterface $quote
     * @param AddressInterface $address
     *
     * @return PickupLocationInterface
     * @throws NoSuchEntityException
     */
    private function getPickupLocation(CartInterface $quote, AddressInterface $address): PickupLocationInterface
    {
        return $this->getPickupLocation->execute(
            $address->getExtensionAttributes()->getPickupLocationCode(),
            SalesChannelInterface::TYPE_WEBSITE,
            $this->getWebsiteCodeByStoreId->execute((int)$quote->getStoreId())
        );
    }
}
