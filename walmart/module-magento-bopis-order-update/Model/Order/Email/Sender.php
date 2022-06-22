<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderNotification\SaveOrderNotification;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\InventoryInStorePickupSales\Model\Order\Email\ReadyForPickupSender as OrderEmailSender;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Psr\Log\LoggerInterface;
use Walmart\BopisInventorySourceApi\Model\Source\Renderer as SourceRenderer;
use Walmart\BopisInventorySourceApi\Model\Configuration;
use Walmart\BopisLocationCheckIn\Api\CheckInHashProviderInterface;
use Walmart\BopisInventorySourceApi\Model\InventorySource;

class Sender extends OrderEmailSender
{
    /**
     * @var SourceRenderer
     */
    private SourceRenderer $sourceRenderer;

    /**
     * @var PaymentHelper
     */
    protected PaymentHelper $paymentHelper;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var CheckInHashProviderInterface
     */
    private CheckInHashProviderInterface $checkInHashProvider;

    /**
     * @var Url
     */
    private Url $urlHelper;

    /**
     * @var InventorySource
     */
    private InventorySource $inventorySource;

    /**
     * Sender constructor.
     *
     * @param Template                     $templateContainer
     * @param IdentityInterface            $identityContainer
     * @param SenderBuilderFactory         $senderBuilderFactory
     * @param LoggerInterface              $logger
     * @param Renderer                     $addressRenderer
     * @param ManagerInterface             $eventManager
     * @param ScopeConfigInterface         $config
     * @param SaveOrderNotification        $saveOrderNotification
     * @param SourceRenderer               $sourceRenderer
     * @param PaymentHelper                $paymentHelper
     * @param Configuration                $configuration
     * @param CheckInHashProviderInterface $checkInHashProvider
     * @param Url                          $urlHelper
     * @param InventorySource              $inventorySource
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager,
        ScopeConfigInterface $config,
        SaveOrderNotification $saveOrderNotification,
        SourceRenderer $sourceRenderer,
        PaymentHelper $paymentHelper,
        Configuration $configuration,
        CheckInHashProviderInterface $checkInHashProvider,
        Url $urlHelper,
        InventorySource $inventorySource
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $eventManager,
            $config,
            $saveOrderNotification
        );
        $this->sourceRenderer = $sourceRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->configuration = $configuration;
        $this->checkInHashProvider = $checkInHashProvider;
        $this->urlHelper = $urlHelper;
        $this->inventorySource = $inventorySource;
    }

    /**
     * Populate order email template with customer information.
     *
     * @param Order $order
     *
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getIsAlternatePickupContact()) {
            $templateId = $this->identityContainer->getAlternateTemplateId();
            $pickupContact = $order->getExtensionAttributes()->getPickupContact();
            $customerName = $pickupContact->getName();
            $customerEmail = $pickupContact->getEmail();
        } elseif ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
            $customerEmail = $order->getCustomerEmail();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $order->getCustomerName();
            $customerEmail = $order->getCustomerEmail();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($customerEmail);
        $this->templateContainer->setTemplateId($templateId);
    }

    /**
     * return the formatted store address as html
     *
     * @param SourceInterface $source
     *
     * @return string
     */
    protected function getFormattedStoreAddress(SourceInterface $source)
    {
        return $this->sourceRenderer->getFormattedStoreAddressHtml($source);
    }

    /**
     * return the Pickup Instructions as html
     *
     * @param SourceInterface $source
     * @param Order           $order
     *
     * @return mixed
     */
    protected function getPickupInstructions(SourceInterface $source, Order $order)
    {
        if ($order->getExtensionAttributes()->getPickupOption() == "curbside") {
            $pickUpInstructions =
                $this->inventorySource->getCurbsideInstructions($source, (int) $order->getStore()->getWebsiteId());
        } else {
            $pickUpInstructions =
                $this->inventorySource->getStorePickupInstructions($source, (int) $order->getStore()->getWebsiteId());
        }

        return $pickUpInstructions;
    }

    /**
     * return the formatted opening hours as html
     *
     * @param $openingHours
     *
     * @return mixed|string
     */
    protected function getFormattedOpeningHours($openingHours)
    {
        return $this->sourceRenderer->getFormattedOpeningHoursHtml($openingHours);
    }

    /**
     * return the check-in url
     *
     * @param Order $order
     *
     * @return string
     */
    protected function getCheckinUrl(Order $order): string
    {
        $hash = $this->checkInHashProvider->get((int) $order->getId());
        $params = [
            'order_id' => $order->getId(),
            '_scope' => $order->getStore()->getId(),
            'hash'     => $hash
        ];

        return $this->urlHelper->getUrl('sales/checkin/index', $params);
    }

    /**
     * @param Order $order
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getOrderUrl(Order $order)
    {
        $params = [
            'order_id' => $order->getId(),
            '_scope' => $order->getStore()->getId(),
        ];

        return $this->urlHelper->getUrl('sales/order/view', $params);
    }

    /**
     * @param Order $order
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getDelayedSupportUrl(Order $order)
    {
        $params = [
            '_scope' => $order->getStore()->getId(),
        ];

        return $this->urlHelper->getUrl('sample-link', $params);
    }

    /**
     * Get payment info block as html
     *
     * @param Order $order
     *
     * @return string
     * @throws Exception
     */
    protected function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param SourceInterface $location
     * @param Order           $order
     * @return string|null
     */
    protected function getPickupEstimatedTime(SourceInterface $location, Order $order): ?string
    {
        $estimatedPickupTimeLabel = $this->inventorySource->getPickupTimeLabel(
            $location,
            (int) $order->getStore()->getWebsiteId()
        );
        $estimatedPickupLeadTime = $this->inventorySource->getPickupLeadTime(
            $location,
            (int) $order->getStore()->getWebsiteId()
        );

        return __($estimatedPickupTimeLabel, $estimatedPickupLeadTime)->render();
    }
}
