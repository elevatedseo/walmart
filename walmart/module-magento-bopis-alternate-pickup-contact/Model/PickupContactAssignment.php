<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address as AddressResource;

class PickupContactAssignment
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var AddressResource
     */
    private AddressResource $addressResource;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param AddressResource $addressResource
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        AddressResource $addressResource
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->addressResource = $addressResource;
    }

    /**
     * @param CartInterface $quote
     * @param AddressInterface $address
     *
     * @return AddressInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CartInterface $quote, AddressInterface $address): AddressInterface
    {
        $address->setAddressType(PickupContactTypeInterface::TYPE_NAME);
        if ($quote->getExtensionAttributes()->getPickupContact()) {
            /** @var Address $pickupContact */
            $pickupContact = $quote->getExtensionAttributes()->getPickupContact();
            $pickupContact->addData($address->getData());
            $this->addressResource->save($pickupContact);
            return $pickupContact;
        }

        $quote->addAddress($address);
        $this->quoteRepository->save($quote);

        return $address;
    }

    /**
     * @param CartInterface $quote
     *
     * @return void
     */
    public function delete(CartInterface $quote): void
    {
        $pickupContact = $quote->getExtensionAttributes()->getPickupContact();
        if (!$pickupContact) {
            return;
        }

        $quote->removeAddress($pickupContact->getId());
        $quote->setDataChanges(true);
        $this->quoteRepository->save($quote);
    }
}
