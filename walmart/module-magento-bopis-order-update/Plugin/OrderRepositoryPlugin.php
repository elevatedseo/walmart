<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Api\OrderExtensionAttributesRepositoryInterface;

/**
 * Class provides a Plugin on Magento\Sales\Api\OrderRepositoryInterface
 * to add custom extension attributes to the order
 */
class OrderRepositoryPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var OrderExtensionAttributesRepositoryInterface
     */
    private OrderExtensionAttributesRepositoryInterface $orderExtensionAttributesRepository;

    /**
     * @param Config $config
     * @param OrderExtensionAttributesRepositoryInterface $orderExtensionAttributesRepository
     */
    public function __construct(
        Config $config,
        OrderExtensionAttributesRepositoryInterface $orderExtensionAttributesRepository
    ) {
        $this->config = $config;
        $this->orderExtensionAttributesRepository = $orderExtensionAttributesRepository;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $result
     *
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $result): OrderInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $customExtensionAttributes = $this->orderExtensionAttributesRepository
            ->getByOrderId((int)$result->getEntityId());
        $orderExtensionAttributes = $result->getExtensionAttributes();

        if (!$orderExtensionAttributes) {
            return $result;
        }

        $orderExtensionAttributes->setBopisQueueStatus($customExtensionAttributes->getBopisQueueStatus());
        $orderExtensionAttributes->setWmtStsEmailStatus($customExtensionAttributes->getWmtStsEmailStatus());
        $orderExtensionAttributes->setWmtIsShipToStore($customExtensionAttributes->getWmtIsShipToStore());

        $result->setExtensionAttributes($orderExtensionAttributes);

        return $result;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $result
     *
     * @return OrderInterface
     *
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $result): OrderInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $extensionAttributes = $result->getExtensionAttributes();
        $customExtensionAttributes = $this->orderExtensionAttributesRepository
            ->getByOrderId((int)$result->getEntityId());

        $customExtensionAttributes->setOrderId((int)$result->getEntityId());
        $customExtensionAttributes->setBopisQueueStatus($extensionAttributes->getBopisQueueStatus());
        $customExtensionAttributes->setWmtStsEmailStatus($extensionAttributes->getWmtStsEmailStatus());
        $customExtensionAttributes->setWmtIsShipToStore($extensionAttributes->getWmtIsShipToStore());

        $this->orderExtensionAttributesRepository->save($customExtensionAttributes);

        $resultAttributes = $result->getExtensionAttributes();

        $resultAttributes->setBopisQueueStatus($customExtensionAttributes->getBopisQueueStatus());
        $resultAttributes->setWmtStsEmailStatus($customExtensionAttributes->getWmtStsEmailStatus());
        $resultAttributes->setWmtIsShipToStore($customExtensionAttributes->getWmtIsShipToStore());

        $result->setExtensionAttributes($resultAttributes);

        return $result;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param SearchResultsInterface $result
     *
     * @return SearchResultsInterface
     *
     * @throws NoSuchEntityException
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        SearchResultsInterface $result
    ): SearchResultsInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        foreach ($result->getItems() as $order) {
            $customExtensionAttributes = $this->orderExtensionAttributesRepository
                ->getByOrderId((int)$order->getEntityId());
            $orderExtensionAttributes = $order->getExtensionAttributes();

            $orderExtensionAttributes->setBopisQueueStatus($customExtensionAttributes->getBopisQueueStatus());
            $orderExtensionAttributes->setWmtStsEmailStatus($customExtensionAttributes->getWmtStsEmailStatus());
            $orderExtensionAttributes->setWmtIsShipToStore($customExtensionAttributes->getWmtIsShipToStore());

            $order->setExtensionAttributes($orderExtensionAttributes);

        }

        return $result;
    }
}
