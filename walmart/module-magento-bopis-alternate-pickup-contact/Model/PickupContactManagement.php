<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactManagementInterface;

class PickupContactManagement implements PickupContactManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var PickupContactAssignment
     */
    private PickupContactAssignment $pickupContactAssignment;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param PickupContactAssignment $pickupContactAssignment
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        PickupContactAssignment $pickupContactAssignment
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->pickupContactAssignment = $pickupContactAssignment;
    }

    /**
     * @inheritDoc
     */
    public function get(int $cartId): ?AddressInterface
    {
        $quote = $this->quoteRepository->getActive($cartId);

        return $quote->getExtensionAttributes()->getPickupContact();
    }

    /**
     * @inheritDoc
     */
    public function save(int $cartId, AddressInterface $address): AddressInterface
    {
        $quote = $this->quoteRepository->get($cartId);

        return $this->pickupContactAssignment->save($quote, $address);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $cartId): void
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $this->pickupContactAssignment->delete($quote);
    }
}
