<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;

/**
 * Set customer custom attribute "selected_inventory_source"
 * AND "preferred_method" on user Sign In and Registration
 */
class SetLocationAndDeliveryMethodPlugin
{
    const FORM_FIELD_PICKUP_LOCATION = 'selected_pickup_location';
    const FORM_FIELD_DELIVERY_METHOD = 'selected_delivery_method';

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param RequestInterface            $request
     * @param LoggerInterface             $logger
     * @param Config                      $config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RequestInterface $request,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Set Preferred Location and Delivery Method on user "Create Account" action
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @param string|null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ): array {
        if (!$this->config->isEnabled()) {
            return [$customer, $password, $redirectUrl];
        }

        if ($selectedLocation = $this->getPickupLocation()) {
            $customer->setCustomAttribute(
                CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE,
                $selectedLocation
            );
        }

        if ($selectedDeliveryMethod = $this->getDeliveryMethod()) {
            $customer->setCustomAttribute(
                CustomerCustomAttributesInterface::PREFERRED_METHOD,
                $selectedDeliveryMethod
            );
        }

        return [$customer, $password, $redirectUrl];
    }

    /**
     * Set Preferred Location and Preferred Delivery Method on user "Sign In" action
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     */
    public function afterAuthenticate(AccountManagementInterface $subject, CustomerInterface $customer)
    {
        if (!$this->config->isEnabled()) {
            return $customer;
        }

        if ($selectedLocation = $this->getPickupLocation()) {
            $customer->setCustomAttribute(
                CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE,
                $selectedLocation
            );
        }

        if ($deliveryMethod = $this->getDeliveryMethod()) {
            $customer->setCustomAttribute(
                CustomerCustomAttributesInterface::PREFERRED_METHOD,
                $deliveryMethod
            );
        }

        try {
            $this->customerRepository->save($customer);
        } catch (\Exception $exception) {
            $this->logger->error(
                'Can\'t save Location related data after sign in, Customer #' . $customer->getId(),
                [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString()
                ]
            );
        }

        return $customer;
    }

    /**
     * Get Location Code (Source Code) from form
     *
     * @return string|null
     */
    private function getPickupLocation(): ?string
    {
        return $this->request->getParam(self::FORM_FIELD_PICKUP_LOCATION);
    }

    /**
     * Get Delivery Method Code from form
     *
     * @return string|null
     */
    private function getDeliveryMethod(): ?string
    {
        return $this->request->getParam(self::FORM_FIELD_DELIVERY_METHOD);
    }
}
