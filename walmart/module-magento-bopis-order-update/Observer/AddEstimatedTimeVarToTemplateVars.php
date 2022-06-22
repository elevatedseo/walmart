<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Sales\Model\Order;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\InventorySource;

class AddEstimatedTimeVarToTemplateVars implements ObserverInterface
{
    /**
     * @var GetPickupLocationCodeByOrderId
     */
    private GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var InventorySource
     */
    private InventorySource $inventorySource;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId
     * @param SourceRepositoryInterface      $sourceRepository
     * @param InventorySource                $inventorySource
     * @param Config                         $config
     */
    public function __construct(
        GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId,
        SourceRepositoryInterface $sourceRepository,
        InventorySource $inventorySource,
        Config $config
    ) {
        $this->getPickupLocationCodeByOrderId = $getPickupLocationCodeByOrderId;
        $this->sourceRepository = $sourceRepository;
        $this->inventorySource = $inventorySource;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $order = $transport->getOrder();
        $pickupLocationCode = $this->getPickupLocationCodeByOrderId->execute((int)$order->getId());
        if (!$pickupLocationCode) {
            return $this;
        }

        $source = $this->sourceRepository->get($pickupLocationCode);

        $transport['pickupEstimatedTime'] = $this->getPickupEstimatedTime($source, $order);

        return $this;
    }

    /**
     * @param SourceInterface $location
     * @param Order           $order
     * @return string|null
     */
    protected function getPickupEstimatedTime(SourceInterface $location, Order $order): ?string
    {
        $estimatedPickupTimeLabel = $this->inventorySource->getPickupTimeLabel(
            $location,
            (int) $order->getStore()->getWebsiteId()
        );
        $estimatedPickupLeadTime = $this->inventorySource->getPickupLeadTime(
            $location,
            (int) $order->getStore()->getWebsiteId()
        );

        return __($estimatedPickupTimeLabel, $estimatedPickupLeadTime)->render();
    }
}
