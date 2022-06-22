<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\Controller\DeliveryMethod;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Walmart\BopisPreferredLocationFrontend\Model\QuoteAddressManagement;

/**
 * Update quote delivery method data
 */
class Save implements HttpPostActionInterface
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var QuoteAddressManagement
     */
    private QuoteAddressManagement $quoteAddressManagement;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param CartRepositoryInterface $quoteRepository
     * @param CustomerSession $customerSession
     * @param JsonFactory $resultJsonFactory
     * @param QuoteAddressManagement $quoteAddressManagement
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        CartRepositoryInterface $quoteRepository,
        CustomerSession $customerSession,
        JsonFactory $resultJsonFactory,
        QuoteAddressManagement $quoteAddressManagement
    ) {
        $this->context = $context;
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteAddressManagement = $quoteAddressManagement;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $deliveryMethod = $this->request->getParam('method');
        $sourceCode = $this->request->getParam('location_code', null);
        $shippingMethod = $this->request->getParam('shipping_method');
        $quote = $this->quoteAddressManagement->getOrCreateQuote();
        $shouldReload = $this->shouldReload($quote, $deliveryMethod);

        if ($quote->getShippingAddress()
            && in_array($quote->getShippingAddress()->getShippingMethod(), [$deliveryMethod, $shippingMethod], true)
        ) {
            $result->setData([
                'success' => true,
                'reload' => false
            ]);

            return $result;
        }

        try {
            $quoteDeliveryMethodData = [
                'delivery_method' => $deliveryMethod,
                'shipping_method' => $shippingMethod,
                'pickup_location_code' => $sourceCode
            ];

            $this->quoteAddressManagement->deliveryMethodChange($quote, $quoteDeliveryMethodData);
            if ($deliveryMethod === 'instore_pickup' && !empty($sourceCode)) {
                $this->quoteAddressManagement->locationChange($quote, $quoteDeliveryMethodData);
            }

            $this->quoteRepository->save($quote);
        } catch (Exception $e) {
            $result->setData([
                'success' => false,
                'reload' => false,
                'message' => $e->getMessage()
            ]);
            return $result;
        }

        $this->customerSession->setDeliveryMethod($deliveryMethod);
        $result->setData([
            'success' => true,
            'reload' => $shouldReload
        ]);

        return $result;
    }

    /**
     * @param CartInterface $quote
     * @param string|null $deliveryMethod
     *
     * @return bool
     */
    private function shouldReload(CartInterface $quote, ?string $deliveryMethod): bool
    {
        $isCartPage = $this->request->getParam('is_cart_page', false);
        if (!$isCartPage) {
            return false;
        }

        if ($quote->getIsVirtual()) {
            return false;
        }

        if ($quote->getShippingAddress()
            && (
                ($quote->getShippingAddress()->getShippingMethod() !== 'instore_pickup' && $deliveryMethod === 'instore_pickup')
                || ($quote->getShippingAddress()->getShippingMethod() === 'instore_pickup' && $deliveryMethod === 'home')
            )
        ) {
            return true;
        }

        return false;
    }
}
