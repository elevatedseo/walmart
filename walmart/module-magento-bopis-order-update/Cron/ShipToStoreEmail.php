<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdate\Model\Order\Email\ShipToStoreNotifier;
use Walmart\BopisOrderUpdate\Model\Order\IsShipToStore;
use Walmart\BopisOrderUpdate\Model\Order\ProcessShipToStoreOrders;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

/**
 * Collect Order with ShipToStore items and send emails
 */
class ShipToStoreEmail
{
    private const MAX_ORDERS = 50;

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
     * @var int
     */
    private int $maxOrdersPerEmail;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param ProcessShipToStoreOrders $processShipToStoreOrders
     * @param Config                   $config
     * @param int                      $maxOrdersPerEmail
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        ProcessShipToStoreOrders $processShipToStoreOrders,
        Config $config,
        int $maxOrdersPerEmail = self::MAX_ORDERS
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->processShipToStoreOrders = $processShipToStoreOrders;
        $this->config = $config;
        $this->maxOrdersPerEmail = $maxOrdersPerEmail;
    }

    /**
     * Get all orders with Ship To Store products and Pending sts emails
     * AND send email to the admin user/identity
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                OrderInterface::STATUS,
                CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE
            )
            ->addFilter('wmt_sts_email_status', IsShipToStore::EMAIL_STATUS_PENDING)
            ->setPageSize($this->maxOrdersPerEmail);

        $ordersResult = $this->orderRepository->getList($searchCriteria->create());

        $this->processShipToStoreOrders->execute($ordersResult);
    }
}
