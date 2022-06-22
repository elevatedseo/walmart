<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\Quote\ValidationRule;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\InventoryInStorePickupApi\Model\GetPickupLocationInterface;
use Magento\InventoryInStorePickupQuote\Model\GetWebsiteCodeByStoreId;
use Magento\InventoryInStorePickupShippingApi\Model\IsInStorePickupDeliveryCartInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ValidationRules\QuoteValidationRuleInterface;
use Walmart\BopisBase\Model\Config;

/**
 * Validate Quote for In-Store Pickup Delivery Method, check if Pickup Option was selected.
 */
class PickupOptionQuoteValidationRule implements QuoteValidationRuleInterface
{
    /**
     * @var ValidationResultFactory
     */
    private ValidationResultFactory $validationResultFactory;

    /**
     * @var GetPickupLocationInterface
     */
    private GetPickupLocationInterface $getPickupLocation;

    /**
     * @var GetWebsiteCodeByStoreId
     */
    private GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param ValidationResultFactory    $validationResultFactory
     * @param GetPickupLocationInterface $getPickupLocation
     * @param GetWebsiteCodeByStoreId    $getWebsiteCodeByStoreId
     * @param Config                     $config
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        GetPickupLocationInterface $getPickupLocation,
        GetWebsiteCodeByStoreId $getWebsiteCodeByStoreId,
        Config $config
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->getPickupLocation = $getPickupLocation;
        $this->getWebsiteCodeByStoreId = $getWebsiteCodeByStoreId;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function validate(Quote $quote): array
    {

        $validationErrors = [];

        if ($quote->isVirtual()) {
            return [$this->validationResultFactory->create(['errors' => $validationErrors])];
        }

        $address = $quote->getShippingAddress();
        $pickupLocation = $this->getPickupLocation($quote, $address);

        if ($this->config->isEnabled() && $pickupLocation) {
            if (!$address->getExtensionAttributes()->getPickupOption()) {
                $validationErrors[] = __(
                    'Quote does not have Pickup Option assigned. Please select a Pickup Option.'
                );
            }
        }

        return [$this->validationResultFactory->create(['errors' => $validationErrors])];
    }

    /**
     * Get Pickup Location entity, assigned to Shipping Address.
     *
     * @param CartInterface $quote
     * @param AddressInterface $address
     *
     * @return PickupLocationInterface|null
     * @throws NoSuchEntityException
     */
    private function getPickupLocation(CartInterface $quote, AddressInterface $address): ?PickupLocationInterface
    {
        if (!$address->getExtensionAttributes() || !$address->getExtensionAttributes()->getPickupLocationCode()) {
            return null;
        }

        return $this->getPickupLocation->execute(
            $address->getExtensionAttributes()->getPickupLocationCode(),
            SalesChannelInterface::TYPE_WEBSITE,
            $this->getWebsiteCodeByStoreId->execute((int)$quote->getStoreId())
        );
    }
}
