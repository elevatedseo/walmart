<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\ToOrderAddress as ToOrderAddressConverter;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory;
use Magento\Sales\Model\Order;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Walmart\BopisBase\Model\Config;

class ConvertPickupContactToOrderAddress implements ObserverInterface
{
    /**
     * @var ToOrderAddressConverter
     */
    private ToOrderAddressConverter $quoteAddressToOrderAddress;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $addressCollectionFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param ToOrderAddressConverter $quoteAddressToOrderAddress
     * @param CollectionFactory       $addressCollectionFactory
     * @param Config                  $config
     */
    public function __construct(
        ToOrderAddressConverter $quoteAddressToOrderAddress,
        CollectionFactory $addressCollectionFactory,
        Config $config
    ) {
        $this->quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {

        if (!$this->config->isEnabled()) {
            return;
        }

        /** @var Order $order */
        $order = $observer->getData('order');
        /** @var Quote $quote */
        $quote = $observer->getData('quote');

        if (!$quote->getExtensionAttributes()) {
            return;
        }

        if ($quote->getExtensionAttributes()->getPickupContact() === null) {
            return;
        }

        $address = $this->getPickupContactAddress($quote);
        $pickupContact = $this->quoteAddressToOrderAddress->convert(
            $address,
            [
                'address_type' => PickupContactTypeInterface::TYPE_NAME
            ]
        );

        $order->getExtensionAttributes()->setPickupContact($pickupContact);
        $addresses = $order->getAddresses();
        $addresses[] = $pickupContact;
        $order->setAddresses($addresses);
    }

    /**
     * @param Quote $quote
     *
     * @return AddressInterface
     */
    private function getPickupContactAddress(Quote $quote): AddressInterface
    {
        return $this->addressCollectionFactory
            ->create()
            ->addFieldToFilter('main_table.address_id', $quote->getExtensionAttributes()->getPickupContact()->getId())
            ->getFirstItem();
    }
}
