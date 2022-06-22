<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
namespace Walmart\BopisDeliverySelection\Plugin\Checkout\Model;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Ui\Component\Form\Element\Multiline;
use Walmart\BopisBase\Model\Config;

/**
 * Pass shippingAddressFromData and selectedShippingMethod to cart/checkout to pre select address and shipping method
 */
class DefaultConfigProviderPlugin
{
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var AddressMetadataInterface
     */
    private AddressMetadataInterface $addressMetadata;

    /**
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param CheckoutSession          $checkoutSession
     * @param CartRepositoryInterface  $quoteRepository
     * @param CustomerRepository       $customerRepository
     * @param CustomerSession          $customerSession
     * @param AddressMetadataInterface $addressMetadata
     * @param HttpContext              $httpContext
     * @param Config                   $config
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession,
        AddressMetadataInterface $addressMetadata,
        HttpContext $httpContext,
        Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->addressMetadata = $addressMetadata;
        $this->httpContext = $httpContext;
        $this->config = $config;
    }

    /**
     * Return configuration array
     *
     * @param DefaultConfigProvider $subject
     * @param array $result

     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetConfig(DefaultConfigProvider $subject, array $result): array
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!isset($result['isShippingAddressFromDataValid'])) {
            $quoteId = $this->checkoutSession->getQuote()->getId();
            $quote = $this->quoteRepository->getActive($quoteId);

            /** @var Address $shippingAddress */
            $shippingAddress = $quote->getShippingAddress();
            $shippingMethod = $shippingAddress->getShippingMethod();
            if ($shippingMethod) {
                $shippingMethod = explode('_', $shippingMethod);
                $shippingMethodArray = [
                    'carrier_code' => $shippingMethod[0],
                    'method_code' => $shippingMethod[1]
                ];
                $result['selectedShippingMethod'] = $shippingMethodArray;
            }

        }

        if (!$this->isCustomerLoggedIn()
            || !$this->getCustomer()->getAddresses()
            || !isset($result['isShippingAddressFromDataValid'])) {
            $result = array_merge($result, $this->getQuoteAddressData());
        }

        return $result;
    }

    /**
     * Get quote address data for checkout
     *
     * @return array
     */
    private function getQuoteAddressData(): array
    {
        $output = [];
        $quote = $this->checkoutSession->getQuote();

        $shippingAddressFromData = $this->getAddressFromData($quote->getShippingAddress());
        if ($shippingAddressFromData) {
            $output['isShippingAddressFromDataValid'] = $quote->getShippingAddress()->validate() === true;
            $output['shippingAddressFromData'] = $shippingAddressFromData;
        }

        return $output;
    }

    /**
     * Create address data appropriate to fill checkout address form
     *
     * @param AddressInterface $address
     * @return array
     * @throws LocalizedException
     */
    private function getAddressFromData(AddressInterface $address): array
    {
        $addressData = [];
        $attributesMetadata = $this->addressMetadata->getAllAttributesMetadata();
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                continue;
            }
            $attributeCode = $attributeMetadata->getAttributeCode();
            $attributeData = $address->getData($attributeCode);
            if ($attributeData) {
                if ($attributeMetadata->getFrontendInput() === Multiline::NAME) {
                    $attributeData = \is_array($attributeData) ? $attributeData : explode("\n", $attributeData);
                    $attributeData = (object)$attributeData;
                }
                if ($attributeMetadata->isUserDefined()) {
                    $addressData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES][$attributeCode] = $attributeData;
                    continue;
                }
                $addressData[$attributeCode] = $attributeData;
            }
        }
        return $addressData;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     * @codeCoverageIgnore
     */
    private function isCustomerLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Get logged-in customer
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomer(): CustomerInterface
    {
        return $this->customerRepository->getById($this->customerSession->getCustomerId());
    }
}
