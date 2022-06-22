<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterfaceFactory;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;
use Walmart\BopisOrderFaasSync\Model\State;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class OrderPlugin
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var BopisQueueInterfaceFactory
     */
    private BopisQueueInterfaceFactory $bopisQueueInterfaceFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param BopisQueueInterfaceFactory    $bopisQueueInterfaceFactory
     * @param Config                        $config
     */
    public function __construct(
        BopisQueueRepositoryInterface $queueRepository,
        BopisQueueInterfaceFactory $bopisQueueInterfaceFactory,
        Config $config
    ) {
        $this->queueRepository = $queueRepository;
        $this->bopisQueueInterfaceFactory = $bopisQueueInterfaceFactory;
        $this->config = $config;
    }

    /**
     * @param OrderInterface $subject
     * @param callable $proceed
     *
     * @return void
     * @throws LocalizedException
     */
    public function aroundCancel(
        OrderInterface $subject,
        callable $proceed
    ) {
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        if ($subject->getState() === State::ACKNOWLEDGED_CANCELLATION
            || $subject->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {

            return $proceed();
        }

        if ($subject->getStatus() === CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE
            || $subject->getStatus() === CustomOrderStateInterface::ORDER_STATUS_SHIP_TO_STORE_PENDING_CODE) {
            return $proceed();
        }

        if ($subject->canCancel()) {
            try {
                // check if order sync was sent
                // if no then change the queue item status to COMPLETE
                // and proceed with regular cancel
                $item = $this->queueRepository->getByOrderId($subject->getId(), OperationTypeInterface::NEW_ORDER);
                if ($item->getStatus() === Status::NOT_SENT) {
                    $item->setStatus(Status::CANCELED);
                    $this->queueRepository->save($item);

                    return $proceed();
                }
                // @codingStandardsIgnoreStart
            } catch (NoSuchEntityException $exception) {
                // do nothing
            }

            /** @var BopisQueueInterface $queue */
            $queue = $this->bopisQueueInterfaceFactory->create();
            $queue->setStatus(Status::NOT_SENT);
            $queue->setEntityType(EntityType::ENTITY_TYPE_ORDER);
            $queue->setEntityId($subject->getId());
            $queue->setOperationType(OperationTypeInterface::CANCEL_ORDER);
            $this->queueRepository->save($queue);

            $subject->setData('state', State::PENDING_CANCELLATION);
            $subject->addCommentToStatusHistory(
                __('Order added to the BOPIS queue. Waiting on sending the cancellation request.')
            );

            return $subject;
        }

        return $proceed();
    }
    /**
     * @param OrderInterface $subject
     * @param bool $result
     *
     * @return bool
     */
    public function afterCanCancel(
        OrderInterface $subject,
        bool $result
    ): bool {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!$result || $subject->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return $result;
        }

        if ($subject->getStatus() === State::ACKNOWLEDGED_CANCELLATION) {
            return true;
        }

        $status = $subject->getStatus();
        $state = $subject->getState();

        if ($state === State::PENDING_CANCELLATION) {
            return false;
        }

        if ($subject->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return $result;
        }

        if ($status === CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_CODE) {
            return false;
        }

        return $result;
    }

    /**
     * @param OrderInterface $subject
     * @param bool $result
     *
     * @return bool
     */
    public function afterCanCreditmemo(
        OrderInterface $subject,
        bool $result
    ): bool {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($subject->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return $result;
        }

        try {
            $queueItem = $this->queueRepository->getByOrderId($subject->getId(), OperationTypeInterface::CANCEL_ORDER);

            return $queueItem && $subject->getState() === State::PENDING_CANCELLATION;
        } catch (Exception $exception) {
            return $result;
        }

        return $result;
    }
}
