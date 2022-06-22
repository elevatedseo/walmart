<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterfaceFactory;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Backend\Model\Session\Quote as BackendQuote;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

/**
 * Add order to BOPIS queue if location being declared
 */
class PlaceAfterPlugin
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $bopisQueueRepository;

    /**
     * @var BopisQueueInterfaceFactory
     */
    private BopisQueueInterfaceFactory $bopisQueueInterfaceFactory;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $quoteSession;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var IsShipToStore
     */
    private IsShipToStore $isShipToStore;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param BopisQueueRepositoryInterface $bopisQueueRepository
     * @param BopisQueueInterfaceFactory    $bopisQueueInterfaceFactory
     * @param SessionManagerInterface       $quoteSession
     * @param IsShipToStore                 $isShipToStore
     * @param LoggerInterface               $logger
     * @param Config                        $config
     */
    public function __construct(
        BopisQueueRepositoryInterface $bopisQueueRepository,
        BopisQueueInterfaceFactory $bopisQueueInterfaceFactory,
        SessionManagerInterface $quoteSession,
        IsShipToStore $isShipToStore,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->bopisQueueRepository = $bopisQueueRepository;
        $this->bopisQueueInterfaceFactory = $bopisQueueInterfaceFactory;
        $this->quoteSession = $quoteSession;
        $this->isShipToStore = $isShipToStore;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Add order to operation queue if pickup location is present.
     * Don't add to the Queue Ship To Store orders
     *
     * @param OrderManagementInterface $subject
     * @param OrderInterface $order
     *
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterPlace(OrderManagementInterface $subject, $order)
    {
        if (!$this->config->isEnabled()) {
            return $order;
        }

        if ($this->isShipToStore->execute($order)) {
            return $order;
        }

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE
            || $order->getState() !== Order::STATE_PROCESSING) {

            return $order;
        }

        $orderId = $order->getId();
        $pickupLocationCode = $this->getPickupLocationFromSession();
        if ($pickupLocationCode != null) {
            /** @var BopisQueueInterface $queue */
            $queue = $this->bopisQueueInterfaceFactory->create();
            $queue->setStatus(Status::NOT_SENT);
            $queue->setEntityType(EntityType::ENTITY_TYPE_ORDER);
            $queue->setEntityId($orderId);
            $queue->setOperationType(OperationTypeInterface::NEW_ORDER);
            $this->bopisQueueRepository->save($queue);
        } else {
            if ($order->getShippingMethod() === StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
                $this->logger->error(
                    __('Missed Pickup Location for Order #%1, entityID: %2', $order->getIncrementId(), $orderId)
                );
            }
        }

        return $order;
    }

    /**
     * Return the Pickup Location from session if existent.
     *
     * @return string|null
     */
    private function getPickupLocationFromSession(): ?string
    {
        /** @var CheckoutSession|BackendQuote $quote */
        $quote = $this->quoteSession->getQuote();
        if ($quote->getId() && $quote->getShippingAddress()) {
            return $quote->getShippingAddress()->getExtensionAttributes()->getPickupLocationCode();
        }

        return null;
    }
}
