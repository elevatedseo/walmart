<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Controller\Location;

use Exception;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Walmart\BopisPreferredLocationApi\Model\Command\SetCustomerPreferredLocation;
use Walmart\BopisPreferredLocationFrontend\Model\QuoteAddressManagement;

/**
 * Save Preferred Location via Ajax POST request
 */
class Save implements ActionInterface, HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * @var SetCustomerPreferredLocation
     */
    private SetCustomerPreferredLocation $setCustomerPreferredLocation;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var QuoteAddressManagement
     */
    private QuoteAddressManagement $quoteAddressManagement;

    /**
     * @param RequestInterface $request
     * @param CurrentCustomer $currentCustomer
     * @param SetCustomerPreferredLocation $setCustomerPreferredLocation
     * @param CustomerSession $customerSession
     * @param CartRepositoryInterface $quoteRepository
     * @param JsonFactory $resultJsonFactory
     * @param QuoteAddressManagement $quoteAddressManagement
     */
    public function __construct(
        RequestInterface $request,
        CurrentCustomer $currentCustomer,
        SetCustomerPreferredLocation $setCustomerPreferredLocation,
        CustomerSession $customerSession,
        CartRepositoryInterface $quoteRepository,
        JsonFactory $resultJsonFactory,
        QuoteAddressManagement $quoteAddressManagement
    ) {
        $this->request = $request;
        $this->currentCustomer = $currentCustomer;
        $this->setCustomerPreferredLocation = $setCustomerPreferredLocation;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteAddressManagement = $quoteAddressManagement;
    }

    /**
     * Save Preferred Location via Ajax
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $sourceCode = $this->request->getParam('location_code');

        if (!$sourceCode) {
            $result->setData(['success' => false]);
            return $result;
        }

        try {
            if ($this->currentCustomer->getCustomerId()) {
                $this->setCustomerPreferredLocation->execute($this->currentCustomer->getCustomer(), $sourceCode);
            }

            $this->customerSession->setPreferredLocation($sourceCode);

            $quoteData = [
                'delivery_method' => 'instore_pickup',
                'pickup_location_code' => $sourceCode
            ];

            $quote = $this->quoteAddressManagement->getOrCreateQuote();
            $this->quoteAddressManagement->locationChange($quote, $quoteData);
            $this->quoteRepository->save($quote);

            $result->setData([
                'success' => true
            ]);
        } catch (Exception $e) {
            $result->setData([
                'success' => false
            ]);
        }

        return $result;
    }
}
