<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Plugin\Sales\Model;

use Magento\Sales\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Api\OrderExtensionAttributesRepositoryInterface;

/**
 * Class provides after Plugin on Magento\Sales\Model\Order::loadByIncrementId()
 * to add custom extension attributes to the order
 */
class OrderPlugin
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
     * @param Order $subject
     * @param Order $result
     *
     * @return Order
     * @throws NoSuchEntityException
     */
    public function afterLoadByIncrementId(Order $subject, Order $result): Order
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
}
