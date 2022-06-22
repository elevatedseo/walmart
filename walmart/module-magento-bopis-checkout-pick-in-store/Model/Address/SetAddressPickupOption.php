<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\Address;

use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;

class SetAddressPickupOption
{
    /**
     * @var AddressExtensionInterfaceFactory
     */
    private AddressExtensionInterfaceFactory $extensionFactory;

    /**
     * @param AddressExtensionInterfaceFactory $extensionFactory
     */
    public function __construct(
        AddressExtensionInterfaceFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Set Address pickup option
     *
     * @param AddressInterface $address
     * @param string $pickupOption
     *
     * @return void
     */
    public function execute(AddressInterface $address, string $pickupOption): void
    {
        if ($address->getExtensionAttributes() === null) {
            $address->setExtensionAttributes($this->extensionFactory->create());
        }
        $address->getExtensionAttributes()->setPickupOption($pickupOption);
    }
}
