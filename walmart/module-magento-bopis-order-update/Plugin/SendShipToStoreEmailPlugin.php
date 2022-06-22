<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdate\Model\Order\ProcessShipToStoreOrders;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;
use Walmart\BopisOrderUpdateApi\Model\NotifyOrdersAreReadyForPickup;

/**
 * Send Ship To Store notification email before change order status
 *
 * @see \Walmart\BopisOrderUpdateApi\Model\NotifyOrdersAreReadyForPickup
 */
class SendShipToStoreEmailPlugin
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ProcessShipToStoreOrders
     */
    private ProcessShipToStoreOrders $processShipToStoreOrders;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param ProcessShipToStoreOrders $processShipToStoreOrders
     * @param Config                   $config
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        ProcessShipToStoreOrders $processShipToStoreOrders,
        Config $config
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->processShipToStoreOrders = $processShipToStoreOrders;
        $this->config = $config;
    }

    /**
     * @param NotifyOrdersAreReadyForPickup $subject
     * @param array|int[] $orderIds
     * @return array
     */
    public function beforeExecute(NotifyOrdersAreReadyForPickup $subject, array $orderIds): array
    {
        if (!$this->config->isEnabled()) {
            return [$orderIds];
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                OrderInterface::STATUS,
                CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE
            )
            ->addFilter('wmt_sts_email_status', IsShipToStore::EMAIL_STATUS_PENDING)
            ->addFilter('entity_id', $orderIds, 'in');

        $ordersResult = $this->orderRepository->getList($searchCriteria->create());

        $this->processShipToStoreOrders->execute($ordersResult);

        return [$orderIds];
    }
}
