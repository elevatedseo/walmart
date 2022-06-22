<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote;

use Magento\Framework\Api\ExtensibleDataInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStoreAPi\Api\Data\PickupOptionInterface;
use Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrder;

/**
 * Set Pickup Option to the Order from Quote Address.
 *
 * The Pickup Option will be pass to the Order only if selected delivery method is In-Store Pickup.
 */
class SetPickupOptionToOrder
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Add Pickup Option to the Order from Quote Address.
     *
     * @param ToOrder $subject
     * @param Address $address
     * @param array $data
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeConvert(ToOrder $subject, Address $address, array $data = []): array
    {
        if (!$this->config->isEnabled() || $address->getShippingMethod() !== InStorePickup::DELIVERY_METHOD) {
            return [$address, $data];
        }

        $extension = $address->getExtensionAttributes();

        if ($extension && $extension->getPickupOption()) {
            $data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY][PickupOptionInterface::PICKUP_OPTION] =
                $extension->getPickupOption();
        }

        return [$address, $data];
    }
}
