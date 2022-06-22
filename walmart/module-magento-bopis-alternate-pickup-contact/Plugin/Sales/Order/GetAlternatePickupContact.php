<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Plugin\Sales\Order;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Walmart\BopisBase\Model\Config;

class GetAlternatePickupContact
{
    /**
     * @var OrderExtensionFactory
     */
    private OrderExtensionFactory $orderExtensionFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param Config                $config
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        Config $config
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->config = $config;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ): OrderInterface {
        if (!$this->config->isEnabled()) {
            return $order;
        }

        $this->setPickupContact($order);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orderSearchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ): OrderSearchResultInterface {
        if (!$this->config->isEnabled()) {
            return $orderSearchResult;
        }

        $items = $orderSearchResult->getItems();
        array_walk($items, [$this, 'setPickupContact']);

        return $orderSearchResult;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface|void
     */
    private function setPickupContact(OrderInterface $order): void
    {
        $extension = $order->getExtensionAttributes();
        if (null === $extension) {
            $extension = $this->orderExtensionFactory->create();
        }

        if ($extension->getPickupContact()) {
            return;
        }

        $pickupContact = null;
        foreach ($order->getAddressesCollection() as $address) {
            if ($address->getAddressType() === PickupContactTypeInterface::TYPE_NAME) {
                $pickupContact = $address;
                break;
            }
        }

        if ($pickupContact) {
            $extension->setPickupContact($pickupContact);
        }

        $order->setExtensionAttributes($extension);
    }
}
