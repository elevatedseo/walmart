<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Model;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Walmart\BopisPreferredLocationFrontend\Model\QuoteStockValidator;

class QuoteStockValidatorObserver implements ObserverInterface
{
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

        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }
    }
}
