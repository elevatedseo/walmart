<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderNotification\SaveOrderNotification;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Psr\Log\LoggerInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Model\Configuration;
use Walmart\BopisInventorySourceApi\Model\Source\Renderer as SourceRenderer;
use Walmart\BopisLocationCheckIn\Api\CheckInHashProviderInterface;
use Walmart\BopisInventorySourceApi\Model\InventorySource;

/**
 * @inheritdoc
 */
class HasBeenPickedUpSender extends Sender
{
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $eventManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $config;

    /**
     * @var GetPickupLocationCodeByOrderId
     */
    private GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var InventorySourceOpeningHoursRepositoryInterface
     */
    private InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository;

    private SaveOrderNotification $saveOrderNotification;

    /**
     * @param Template                                       $templateContainer
     * @param IdentityInterface                              $identityContainer
     * @param SenderBuilderFactory                           $senderBuilderFactory
     * @param LoggerInterface                                $logger
     * @param Renderer                                       $addressRenderer
     * @param ManagerInterface                               $eventManager
     * @param ScopeConfigInterface                           $config
     * @param SaveOrderNotification                          $saveOrderNotification
     * @param SourceRenderer                                 $sourceRenderer
     * @param PaymentHelper                                  $paymentHelper
     * @param Configuration                                  $configuration
     * @param CheckInHashProviderInterface                   $checkInHashProvider
     * @param Url                                            $urlHelper
     * @param GetPickupLocationCodeByOrderId                 $getPickupLocationCodeByOrderId
     * @param SourceRepositoryInterface                      $sourceRepository
     * @param InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository
     * @param InventorySource                                $inventorySource
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
        GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId,
        SourceRepositoryInterface $sourceRepository,
        InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository,
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
            $saveOrderNotification,
            $sourceRenderer,
            $paymentHelper,
            $configuration,
            $checkInHashProvider,
            $urlHelper,
            $inventorySource
        );

        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->getPickupLocationCodeByOrderId = $getPickupLocationCodeByOrderId;
        $this->sourceRepository = $sourceRepository;
        $this->inventorySourceOpeningHoursRepository = $inventorySourceOpeningHoursRepository;
    }

    /**
     * Send order-specific email.
     * This method is not declared anywhere in parent/interface, but Magento calls it.
     *
     * @param OrderInterface $order
     * @param bool           $forceSyncMode
     *
     * @return bool
     */
    public function send(OrderInterface $order, $forceSyncMode = false): bool
    {
        $result = false;

        if (!$this->config->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            $result = $this->checkAndSend($order);
        }

        return $result;
    }

    /**
     * Prepare email template with variables
     *
     * @param Order $order
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function prepareTemplate(Order $order)
    {
        $pickupLocationCode = $this->getPickupLocationCodeByOrderId->execute((int) $order->getId());
        $source = $this->sourceRepository->get($pickupLocationCode);
        $openingHours = $this->inventorySourceOpeningHoursRepository->getListBySourceCode($pickupLocationCode);

        $transport = [
            'order'                    => $order,
            'order_id'                 => $order->getId(),
            'order_url'                => $this->getOrderUrl($order),
            'billing'                  => $order->getBillingAddress(),
            'payment_html'             => $this->getPaymentHtml($order),
            'checkin_url'              => $this->getCheckinUrl($order),
            'store'                    => $order->getStore(),
            'formattedStoreAddress'    => $this->getFormattedStoreAddress($source),
            'formattedOpeningHours'    => $this->getFormattedOpeningHours($openingHours),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress'  => $this->getFormattedBillingAddress($order),
            'pickupInstructions'       => $this->getPickupInstructions($source, $order),
            'created_at_formatted'     => $order->getCreatedAtFormatted(2),
            'order_data'               => [
                'customer_name'         => $order->getCustomerName(),
                'is_not_virtual'        => $order->getIsNotVirtual(),
                'email_customer_note'   => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];
        $transportObject = new DataObject($transport);

        /**
         * Event argument `transport` is @deprecated. Use `transportObject` instead.
         */
        $this->eventManager->dispatch(
            'email_has_been_picked_up_set_template_vars_before',
            ['sender' => $this, 'transport' => $transportObject, 'transportObject' => $transportObject]
        );

        $this->templateContainer->setTemplateVars($transportObject->getData());

        parent::prepareTemplate($order);
    }
}
