<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface;
use Walmart\BopisOrderFaasSync\Api\CreationConfirmedInterface;
use Walmart\BopisOrderFaasSync\Api\ConfirmationStatusInterface;

class CreationConfirmed implements CreationConfirmedInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var OrderInterfaceFactory
     */
    private OrderInterfaceFactory $orderFactory;

    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterfaceFactory $orderFactory
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param Logger $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderInterfaceFactory $orderFactory,
        BopisQueueRepositoryInterface $queueRepository,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->queueRepository = $queueRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute(
        string $orderId,
        string $customerOrderId,
        ConfirmationStatusInterface $status,
        ?string $orderSource = null,
        ?string $storeNumber = null
    ): void {
        $this->logger->info(
            'Received Order Sync ACK',
            [
                'orderId' => $orderId,
                'status' => $status->getDescription()
            ]
        );

        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if (!$order->getId()) {
            throw new Exception(
                __("The entity that was requested doesn't exist. Verify the entity and try again."),
                null,
                Exception::HTTP_NOT_FOUND
            );
        }

        if ($status->getDescription() === ReasonInterface::STATUS_CREATED) {
            $order->getExtensionAttributes()->setBopisQueueStatus(Status::COMPLETED);
            $order->addCommentToStatusHistory(
                __('Order was successfully sync with BOPIS solution.')
            );
            $this->orderRepository->save($order);

            try {
                $queueItem = $this->queueRepository->getByOrderId(
                    $order->getId(),
                    OperationTypeInterface::NEW_ORDER
                );
                $queueItem->setStatus(Status::COMPLETED);
                $this->queueRepository->save($queueItem);
            } catch (NoSuchEntityException $exception) {
                $this->logger->error(
                    "Queue Item for selected order doesn't exist",
                    [
                        'increment_id' => $orderId
                    ]
                );
            } catch (\Exception $exception) {
                $this->logger->error(
                    'There was a problem with changing the status of the queue item.',
                    [
                        'increment_id' => $orderId,
                        'msg' => $exception->getMessage()
                    ]
                );
            }
            return;
        }

        $order->getExtensionAttributes()->setBopisQueueStatus(Status::FAILED);
        $order->addCommentToStatusHistory(
            $this->getErrorMessage($status)
        );
        $this->orderRepository->save($order);
    }

    /**
     * @param ConfirmationStatusInterface $status
     *
     * @return string
     */
    private function getErrorMessage(ConfirmationStatusInterface $status): string
    {
        $error = 'There was a problem with the BOPIS solution sync.';
        if ($status->getReason() && $status->getReason()->getDescription()) {
            $error .= ' Description: ' . $status->getReason()->getDescription();
        }

        return $error;
    }
}
