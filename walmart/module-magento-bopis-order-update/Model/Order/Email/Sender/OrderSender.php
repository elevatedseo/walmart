<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email\Sender;

use Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source\PreferredMethodSource;
use Walmart\BopisOrderUpdate\Model\Order\Email\Container\OrderInStoreIdentity;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as Sender;

/**
 * We need to replace identity container depends on Order Delivery method to be able to configure different
 * templates for "New Order" email
 *
 * - 'sales_email_order_template'/'sales_email_order_guest_template' should be used for free shipping,
 * table rates, etc
 *
 * - 'sales_email_order_in_store_template'/'sales_email_order_in_store_guest_template' should be used only
 * for In Store Delivery
 *
 */
class OrderSender extends Sender
{
    /**
     * @var OrderInStoreIdentity
     */
    protected $orderInStoreIdentity;

    /**
     * @param Template $templateContainer
     * @param OrderIdentity $identityContainer
     * @param Order\Email\SenderBuilderFactory $senderBuilderFactory
     * @param LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param OrderResource $orderResource
     * @param ScopeConfigInterface $globalConfig
     * @param ManagerInterface $eventManager
     * @param OrderInStoreIdentity $orderInStoreIdentity
     */
    public function __construct(
        Template $templateContainer,
        OrderIdentity $identityContainer,
        Order\Email\SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger, Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        ScopeConfigInterface $globalConfig,
        ManagerInterface $eventManager,
        OrderInStoreIdentity $orderInStoreIdentity
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $orderResource,
            $globalConfig,
            $eventManager
        );

        $this->orderInStoreIdentity = $orderInStoreIdentity;
    }

    /**
     * @param Order $order
     * @param false $forceSyncMode
     * @return bool
     */
    public function send(Order $order, $forceSyncMode = false)
    {
        if ($order->getShippingMethod() == PreferredMethodSource::STORE_PICKUP_CODE) {
            $this->identityContainer = $this->orderInStoreIdentity;
        }

        return parent::send($order, $forceSyncMode);
    }

    /**
     * Send order email if it is enabled in configuration.
     *
     * @param Order $order
     * @return bool|void
     */
    protected function checkAndSend(Order $order)
    {
        if ($order->getShippingMethod() == PreferredMethodSource::STORE_PICKUP_CODE) {
            $this->identityContainer = $this->orderInStoreIdentity;
        }

        parent::checkAndSend($order);
    }

    /**
     * @param Order $order
     */
    protected function prepareTemplate(Order $order)
    {
        if ($order->getShippingMethod() == PreferredMethodSource::STORE_PICKUP_CODE) {
            $this->identityContainer = $this->orderInStoreIdentity;
        }

        parent::prepareTemplate($order);
    }
}
