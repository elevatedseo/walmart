<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote;

use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\InventoryInStorePickupQuote\Model\ToQuoteAddress;
use Magento\Quote\Api\Data\AddressInterface;
use Walmart\BopisBase\Model\Config;

/**
 * Assign selected Pickup Option to the new address when converting address
 */
class AssignmentOfPickupOptionWhenConvertingAddress
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
     * @param ToQuoteAddress          $subject
     * @param AddressInterface        $result
     * @param PickupLocationInterface $pickupLocation
     * @param AddressInterface        $originalAddress
     *
     * @return AddressInterface
     */
    public function afterConvert(
        ToQuoteAddress $subject,
        AddressInterface $result,
        PickupLocationInterface $pickupLocation,
        AddressInterface $originalAddress
    ): AddressInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $extension = $originalAddress->getExtensionAttributes();

        if ($extension && $extension->getPickupOption()) {
            $result->getExtensionAttributes()->setPickupOption($extension->getPickupOption());
        }

        return $result;
    }
}
