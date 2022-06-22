<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Plugin\Checkout;

use Exception;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ResourceModel\Quote\Address as AddressResource;
use Walmart\BopisAlternatePickupContact\Model\PickupContactAssignment;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Walmart\BopisBase\Model\Config;

class ManagePickupContact
{
    /**
     * @var AddressResource
     */
    private AddressResource $addressResource;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @var PickupContactAssignment
     */
    private PickupContactAssignment $pickupContactAssignment;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param AddressResource         $addressResource
     * @param CartRepositoryInterface $cartRepository
     * @param PickupContactAssignment $pickupContactAssignment
     * @param Config                  $config
     */
    public function __construct(
        AddressResource $addressResource,
        CartRepositoryInterface $cartRepository,
        PickupContactAssignment $pickupContactAssignment,
        Config $config
    ) {
        $this->addressResource = $addressResource;
        $this->cartRepository = $cartRepository;
        $this->pickupContactAssignment = $pickupContactAssignment;
        $this->config = $config;
    }

    /**
     * @param ShippingInformationManagementInterface $subject
     * @param PaymentDetailsInterface $result
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return PaymentDetailsInterface
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function afterSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        PaymentDetailsInterface $result,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): PaymentDetailsInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        try {
            $quote = $this->cartRepository->getActive($cartId);
            // avoid duplicates
            $this->pickupContactAssignment->delete($quote);

            // save alternate pickup contact entity
            if ($addressInformation->getExtensionAttributes()->getAltPickupContact()) {
                /** @var Address $pickupContact */
                $pickupContact = $addressInformation->getExtensionAttributes()->getAltPickupContact();
                $pickupContact->setAddressType(PickupContactTypeInterface::TYPE_NAME);
                $pickupContact->setQuoteId($quote->getId());
                $this->addressResource->save($pickupContact);
            }

            return $result;
        } catch (Exception $exception) {
            return $result;
        }
    }
}
