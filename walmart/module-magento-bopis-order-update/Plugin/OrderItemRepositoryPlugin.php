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
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Api\OrderItemExtensionAttributesRepositoryInterface;

/**
 * Class provides a Plugin on Magento\Sales\Api\OrderItemRepositoryInterface
 * to add custom extension attributes to the order item
 */
class OrderItemRepositoryPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var OrderItemExtensionAttributesRepositoryInterface
     */
    private OrderItemExtensionAttributesRepositoryInterface $orderItemExtensionAttributesRepository;

    /**
     * @param Config $config
     * @param OrderItemExtensionAttributesRepositoryInterface $orderItemExtensionAttributesRepository
     */
    public function __construct(
        Config $config,
        OrderItemExtensionAttributesRepositoryInterface $orderItemExtensionAttributesRepository
    ) {
        $this->config = $config;
        $this->orderItemExtensionAttributesRepository = $orderItemExtensionAttributesRepository;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $result
     *
     * @return OrderItemInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $result): OrderItemInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $customExtensionAttributes = $this->orderItemExtensionAttributesRepository
            ->getByOrderItemId((int)$result->getItemId());
        $orderExtensionAttributes = $result->getExtensionAttributes();

        if (!$orderExtensionAttributes) {
            return $result;
        }

        $orderExtensionAttributes->setWmtShipToStore($customExtensionAttributes->getWmtShipToStore());
        $orderExtensionAttributes->setWmtItemPickedStatus($customExtensionAttributes->getWmtItemPickedStatus());
        $orderExtensionAttributes->setWmtItemDispensedStatus($customExtensionAttributes->getWmtItemDispensedStatus());

        $result->setExtensionAttributes($orderExtensionAttributes);

        return $result;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $result
     *
     * @return OrderItemInterface
     *
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function afterSave(OrderItemRepositoryInterface $subject, OrderItemInterface $result): OrderItemInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $extensionAttributes = $result->getExtensionAttributes();
        $customExtensionAttributes = $this->orderItemExtensionAttributesRepository
            ->getByOrderItemId((int)$result->getItemId());

        $customExtensionAttributes->setOrderItemId((int)$result->getItemId());
        $customExtensionAttributes->setWmtShipToStore($extensionAttributes->getWmtShipToStore());
        $customExtensionAttributes->setWmtItemPickedStatus($extensionAttributes->getWmtItemPickedStatus());
        $customExtensionAttributes->setWmtItemDispensedStatus($extensionAttributes->getWmtItemDispensedStatus());

        $this->orderItemExtensionAttributesRepository->save($customExtensionAttributes);

        $resultAttributes = $result->getExtensionAttributes();

        $resultAttributes->setWmtShipToStore($customExtensionAttributes->getWmtShipToStore());
        $resultAttributes->setWmtItemPickedStatus($customExtensionAttributes->getWmtItemPickedStatus());
        $resultAttributes->setWmtItemDispensedStatus($customExtensionAttributes->getWmtItemDispensedStatus());

        $result->setExtensionAttributes($resultAttributes);

        return $result;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param SearchResultsInterface $result
     *
     * @return SearchResultsInterface
     *
     * @throws NoSuchEntityException
     */
    public function afterGetList(
        OrderItemRepositoryInterface $subject,
        SearchResultsInterface $result
    ): SearchResultsInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }
        foreach ($result->getItems() as $orderItem) {
            $customExtensionAttributes = $this->orderItemExtensionAttributesRepository
                ->getByOrderItemId((int)$orderItem->getId());
            $orderExtensionAttributes = $orderItem->getExtensionAttributes();

            $orderExtensionAttributes->setWmtShipToStore($customExtensionAttributes->getWmtShipToStore());
            $orderExtensionAttributes->setWmtItemPickedStatus($customExtensionAttributes->getWmtItemPickedStatus());
            $orderExtensionAttributes->setWmtItemDispensedStatus($customExtensionAttributes->getWmtItemDispensedStatus());

            $orderItem->setExtensionAttributes($orderExtensionAttributes);
        }

        return $result;
    }
}
