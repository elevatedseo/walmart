<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisLocationCheckIn\Model\CheckIn;
use Walmart\BopisLocationCheckIn\Service\SendCheckIn;
use Walmart\BopisLogging\Logger\Logger;

class AfterSaveCheckIn implements ObserverInterface
{
    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository;

    /**
     * @var OrderStatusHistoryInterfaceFactory
     */
    private OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var SendCheckIn
     */
    private SendCheckIn $sendCheckInService;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SendCheckIn                           $sendCheckInService
     * @param OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository
     * @param OrderStatusHistoryInterfaceFactory    $orderStatusHistoryFactory
     * @param Logger                                $logger
     * @param Config                                $config
     */
    public function __construct(
        SendCheckIn $sendCheckInService,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory,
        Logger $logger,
        Config $config
    ) {
        $this->sendCheckInService = $sendCheckInService;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        /** @var CheckIn $checkIn */
        $checkIn = $observer->getData('check_in');

        $this->sendCheckInService->execute($checkIn);

        if ($checkIn->isObjectNew()) {
            $this->addCommentToTheOrder($checkIn, (string)__('The check-in has been started'));
            return;
        }

        $this->addCommentToTheOrder($checkIn, (string)__('Updated check-in has been sent to the BOPIS'));
    }

    /**
     * @param CheckIn $checkIn
     * @param string $comment
     *
     * @return void
     */
    private function addCommentToTheOrder(CheckIn $checkIn, string $comment): void
    {
        try {
            /** @var OrderStatusHistoryInterface $historyItem */
            $historyItem = $this->orderStatusHistoryFactory->create();
            $historyItem->setParentId($checkIn->getOrderId());
            $historyItem->setIsCustomerNotified(false);
            $historyItem->setIsVisibleOnFront(false);
            $historyItem->setComment($comment);

            $this->orderStatusHistoryRepository->save($historyItem);
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with adding the comment to the order',
                [
                'order_id' => $checkIn->getOrderId()
                ]
            );
        }
    }
}
