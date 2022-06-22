<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model\Address;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManager;
use Magento\InventoryInStorePickupQuote\Model\Address\GetAddressPickupLocationCode;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\Collection as AddressCollection;
use Walmart\BopisDeliverySelection\Model\GetSelectedDeliveryMethod;

/**
 * Adapter for getting Pickup Location Code from Quote address
 */
class GetPickupLocationCodeAdapter
{
    /**
     * @var Quote|null
     */
    private ?Quote $quote;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var GetAddressPickupLocationCode
     */
    private GetAddressPickupLocationCode $getAddressPickupLocationCode;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param SessionManager $session
     * @param GetAddressPickupLocationCode $getAddressPickupLocationCode
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Session $session,
        GetAddressPickupLocationCode $getAddressPickupLocationCode,
        CollectionFactory $collectionFactory
    ) {
        $this->getAddressPickupLocationCode = $getAddressPickupLocationCode;
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;

        $this->quote = null;
    }

    /**
     * @param Quote|null $quote
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPreferredLocation(?Quote $quote = null): ?string
    {
        try {

            $quoteId = $quote ? $quote->getEntityId() : $this->session->getQuoteId();
            if (!$quoteId) {
                return null;
            }

            /** @var AddressCollection $addressesCollection */
            $addressesCollection = $this->collectionFactory->create();
            $addressesCollection->addFieldToFilter('quote_id', $quoteId);
            $addressesCollection->addFilter('address_type', Address::ADDRESS_TYPE_SHIPPING);

            $shippingAddress = $addressesCollection->getFirstItem();

            if ($shippingAddress === null) {
              return null;
            }

            return $this->getAddressPickupLocationCode->execute($shippingAddress);
        }
        catch (\Exception $e)
        {
            return null;
        }


    }
}
