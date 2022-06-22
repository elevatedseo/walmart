<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote\Address;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStore\Model\ResourceModel\DeleteQuoteAddressPickupOption;
use Walmart\BopisCheckoutPickInStore\Model\ResourceModel\SaveQuoteAddressPickupOption;
use Magento\Quote\Model\Quote\Address;

/**
 * Save or delete selected Pickup Option for Quote Address.
 */
class ManageAssignmentOfPickupOptionToQuoteAddress
{
    /**
     * @var SaveQuoteAddressPickupOption
     */
    private SaveQuoteAddressPickupOption $saveQuoteAddressPickupOption;

    /**
     * @var DeleteQuoteAddressPickupOption
     */
    private DeleteQuoteAddressPickupOption $deleteQuoteAddressPickupOption;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SaveQuoteAddressPickupOption   $saveQuoteAddressPickupOption
     * @param DeleteQuoteAddressPickupOption $deleteQuoteAddressPickupOption
     * @param Config                         $config
     */
    public function __construct(
        SaveQuoteAddressPickupOption $saveQuoteAddressPickupOption,
        DeleteQuoteAddressPickupOption $deleteQuoteAddressPickupOption,
        Config $config
    ) {
        $this->saveQuoteAddressPickupOption = $saveQuoteAddressPickupOption;
        $this->deleteQuoteAddressPickupOption = $deleteQuoteAddressPickupOption;
        $this->config = $config;
    }

    /**
     * Save information about Pickup Option to Quote Address.
     *
     * @param Address $subject
     * @param Address $result
     *
     * @return Address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAfterSave(Address $subject, Address $result): Address
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!$this->validateAddress($subject)) {
            return $result;
        }

        if (!$subject->getExtensionAttributes()->getPickupOption()) {
            $this->deleteQuoteAddressPickupOption->execute((int)$subject->getId());

            return $result;
        }

        $this->saveQuoteAddressPickupOption->execute(
            (int)$subject->getId(),
            $subject->getExtensionAttributes()->getPickupOption()
        );

        return $result;
    }

    /**
     * Check if address can have a Pickup Option.
     *
     * @param Address $address
     *
     * @return bool
     */
    private function validateAddress(Address $address): bool
    {
        return $address->getExtensionAttributes() && $address->getAddressType() === Address::ADDRESS_TYPE_SHIPPING;
    }
}
