<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote\Address;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStore\Model\ResourceModel\GetPickupOptionByQuoteAddressId;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address;

class LoadPickupOptionForQuoteAddress
{
    /**
     * @var GetPickupOptionByQuoteAddressId
     */
    private GetPickupOptionByQuoteAddressId $getPickupOptionByQuoteAddressId;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $addressExtensionInterfaceFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param GetPickupOptionByQuoteAddressId  $getPickupOptionByQuoteAddressId
     * @param AddressExtensionInterfaceFactory $addressExtensionInterfaceFactory
     * @param Config                           $config
     */
    public function __construct(
        GetPickupOptionByQuoteAddressId $getPickupOptionByQuoteAddressId,
        AddressExtensionInterfaceFactory $addressExtensionInterfaceFactory,
        Config $config
    ) {
        $this->getPickupOptionByQuoteAddressId = $getPickupOptionByQuoteAddressId;
        $this->addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
        $this->config = $config;
    }

    /**
     * Load and add Pickup Option information to Quote Address.
     *
     * @param Address $subject
     * @param Address $result
     * @param AbstractModel $entity
     *
     * @return Address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad(Address $subject, Address $result, AbstractModel $entity): Address
    {
        if (!$this->config->isEnabled() || !$entity->getId()) {
            return $result;
        }

        $pickupOption = $this->getPickupOptionByQuoteAddressId->execute((int)$entity->getId());
        if (!$pickupOption) {
            return $result;
        }

        if (!$entity->getExtensionAttributes()) {
            $entity->setExtensionAttributes($this->addressExtensionInterfaceFactory->create());
        }

        $entity->getExtensionAttributes()->setPickupOption($pickupOption);

        return $result;
    }
}
