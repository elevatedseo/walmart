<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote\Address;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryInStorePickupApi\Model\GetPickupLocationInterface;
use Magento\InventoryInStorePickupQuote\Model\Address\GetAddressPickupLocationCode;
use Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\TotalsCollector;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\Configuration;

/**
 * Set Shipping Description e.g. Pickup Option Title - Pickup Location Name
 */
class SetShippingDescription
{
    /**
     * @var GetPickupLocationInterface
     */
    private GetPickupLocationInterface $getPickupLocation;

    /**
     * @var GetAddressPickupLocationCode
     */
    private GetAddressPickupLocationCode $getAddressPickupLocationCode;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param GetPickupLocationInterface   $getPickupLocation
     * @param GetAddressPickupLocationCode $getAddressPickupLocationCode
     * @param Configuration                $configuration
     * @param Config                       $config
     */
    public function __construct(
        GetPickupLocationInterface $getPickupLocation,
        GetAddressPickupLocationCode $getAddressPickupLocationCode,
        Configuration $configuration,
        Config $config
    ) {
        $this->getPickupLocation = $getPickupLocation;
        $this->getAddressPickupLocationCode = $getAddressPickupLocationCode;
        $this->configuration = $configuration;
        $this->config = $config;
    }

    /**
     * Set shipping description to the quote api
     *
     * @param TotalsCollector $subject
     * @param Total $total
     * @param Quote $quote
     * @return Total
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCollect(
        TotalsCollector $subject,
        Total $total,
        Quote $quote
    ) {
        if (!$this->config->isEnabled()) {
            return $total;
        }

        $address = $quote->getShippingAddress();
        if ($address->getShippingMethod() === InStorePickup::DELIVERY_METHOD
            && $this->getAddressPickupLocationCode->execute($address)
        ) {
            if ($option = $address->getExtensionAttributes()->getPickupOption()) {

                $optionTitle = $this->getPickupOptionTitle($option, (int)$quote->getStore()->getWebsite()->getId());

                $locationName = $this->getPickupLocationName(
                    $this->getAddressPickupLocationCode->execute($address),
                    SalesChannelInterface::TYPE_WEBSITE,
                    $quote->getStore()->getWebsite()->getCode()
                );
                $description = sprintf('%s - %s', $optionTitle, $locationName);

                $total->setShippingDescription($description);
                foreach ($quote->getAllAddresses() as $address) {
                    $address->setShippingDescription($description);
                }
            }
        }

        return $total;
    }

    /**
     * Get Pickup Option title
     *
     * @param string $optionCode
     * @param int|null $websiteId
     * @return string
     */
    private function getPickupOptionTitle(string $optionCode, int $websiteId = null)
    {
        if ($optionCode == 'curbside') {
            return $this->configuration->getCurbsideTitle($websiteId);
        }

        return $this->configuration->getInStorePickupTitle($websiteId);
    }

    /**
     * Get Pickup Location name
     *
     * @param string $pickupLocationCode
     * @param string $salesChannelType
     * @param string $salesChannelCode
     * @return string
     * @throws NoSuchEntityException
     */
    private function getPickupLocationName(
        string $pickupLocationCode,
        string $salesChannelType,
        string $salesChannelCode
    ) :string {
        return $this->getPickupLocation->execute(
            $pickupLocationCode,
            $salesChannelType,
            $salesChannelCode
        )->getName();
    }
}
