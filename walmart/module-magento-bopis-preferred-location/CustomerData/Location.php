<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Inventory\Model\Source\Command\GetInterface;
use Walmart\BopisDeliverySelection\Model\GetSelectedDeliveryMethod;
use Walmart\BopisPreferredLocation\Model\GetSelectedLocation;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;
use Walmart\BopisPreferredLocationApi\Model\Command\GetPickupLocations;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Add selected inventory source code and delivery method to CustomerData
 */
class Location implements SectionSourceInterface
{
    private const KEY_SELECTED_INVENTORY_SOURCE = 'preferred_location';

    /**
     * @var GetSelectedDeliveryMethod
     */
    private GetSelectedDeliveryMethod $getSelectedDeliveryMethod;

    /**
     * @var GetSelectedLocation
     */
    private GetSelectedLocation $getSelectedLocation;

    /**
     * @var GetInterface
     */
    private GetInterface $sourceGetter;

    /**
     * @var GetPickupLocations
     */
    private GetPickupLocations $getPickupLocations;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @param GetSelectedDeliveryMethod $getSelectedDeliveryMethod
     * @param GetSelectedLocation $getSelectedLocation
     * @param GetInterface $sourceGetter
     * @param GetPickupLocations $getPickupLocations
     */
    public function __construct(
        GetSelectedDeliveryMethod $getSelectedDeliveryMethod,
        GetSelectedLocation $getSelectedLocation,
        GetInterface $sourceGetter,
        GetPickupLocations $getPickupLocations,
        CustomerSession $customerSession
    ) {
        $this->getSelectedDeliveryMethod = $getSelectedDeliveryMethod;
        $this->getSelectedLocation = $getSelectedLocation;
        $this->sourceGetter = $sourceGetter;
        $this->getPickupLocations = $getPickupLocations;
        $this->customerSession = $customerSession;
    }

    /**
     * Add PickupLocation data to CustomerData
     *
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        $deliveryMethod = $this->getSelectedDeliveryMethod->execute() ?: $this->customerSession->getDeliveryMethod();
        $preferredLocationCode = $this->getSelectedLocation->execute() ?: $this->customerSession->getPreferredLocation();

        $pickupLocation = null;
        if ($preferredLocationCode) {
            $source = $this->sourceGetter->execute($preferredLocationCode);
            $pickupLocation = $this->getPickupLocations->execute([$source]);
            $pickupLocation = array_shift($pickupLocation);
        }

        return [
            self::KEY_SELECTED_INVENTORY_SOURCE => is_object($pickupLocation) ? $pickupLocation->getData() : '',
            CustomerCustomAttributesInterface::PREFERRED_METHOD => $deliveryMethod ?? ''
        ];
    }
}
