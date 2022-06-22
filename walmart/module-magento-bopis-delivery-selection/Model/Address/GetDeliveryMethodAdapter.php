<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\Model\Address;

use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Walmart\BopisDeliverySelection\Model\GetSelectedDeliveryMethod;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\Collection as AddressCollection;

/**
 * Adapter for getting Delivery Method from Quote address
 */
class GetDeliveryMethodAdapter
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
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session,
        CollectionFactory $collectionFactory
    ) {
        $this->quote = null;
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Quote|null $quote
     * @return string|null
     */
    public function getDeliveryMethod(?Quote $quote = null): ?string
    {
        try {
            $quoteId = $quote ? $quote->getEntityId() : $this->session->getQuoteId();
            if (!$quoteId) {
                return null;
            }

            /** @var AddressCollection $addressesCollection */
            $addressesCollection = $this->collectionFactory->create();
            $addressesCollection->setQuoteFilter($quoteId);
            $addressesCollection->addFilter('address_type', Address::ADDRESS_TYPE_SHIPPING);

            $shippingAddress = $addressesCollection->getFirstItem();

            if ($shippingAddress === null) {
                return null;
            }

            return $shippingAddress->getShippingMethod();
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}
