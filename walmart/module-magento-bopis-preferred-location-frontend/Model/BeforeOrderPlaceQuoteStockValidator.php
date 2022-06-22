<?php


/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class BeforeOrderPlaceQuoteStockValidator implements ObserverInterface {

    /**
     * @var Session
     */
    private CheckoutSession $session;

    /**
     * @var QuoteStockValidator
     */
    private QuoteStockValidator $quoteStockValidator;

    public function __construct(CheckoutSession $session, QuoteStockValidator $quoteStockValidator)
    {
        $this->session = $session;
        $this->quoteStockValidator = $quoteStockValidator;
    }

    public function execute(Observer $observer)
    {
        try {
            $quote = $this->session->getQuote();

            $this->quoteStockValidator->execute($quote);

            $quote->validateBeforeSave();
        } catch (NoSuchEntityException $e) {
        }
    }
}
