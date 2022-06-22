<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\Quote;
use Walmart\BopisAlternatePickupContactApi\Api\GuestPickupContactManagementInterface;

class GuestPickupContactManagement implements GuestPickupContactManagementInterface
{
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var PickupContactAssignment
     */
    private PickupContactAssignment $pickupContactAssignment;

    /**
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param CartRepositoryInterface $quoteRepository
     * @param PickupContactAssignment $pickupContactAssignment
     */
    public function __construct(
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CartRepositoryInterface $quoteRepository,
        PickupContactAssignment $pickupContactAssignment
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->quoteRepository = $quoteRepository;
        $this->pickupContactAssignment = $pickupContactAssignment;
    }

    /**
     * @inheritDoc
     */
    public function get(string $cartId): ?AddressInterface
    {
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        $quote = $this->quoteRepository->getActive($quoteId);

        return $quote->getExtensionAttributes()->getPickupContact();
    }

    /**
     * @inheritDoc
     */
    public function save(string $cartId, AddressInterface $address): AddressInterface
    {
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($quoteId);

        return $this->pickupContactAssignment->save($quote, $address);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $cartId): void
    {
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($quoteId);

        $this->pickupContactAssignment->delete($quote);
    }
}
