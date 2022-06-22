<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisOrderUpdate\Block\Order\Email;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Block\Items\AbstractItems;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class ItemsPartlyCanceled extends AbstractItems
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Context                       $context
     * @param array                         $data
     * @param OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
        Context $context,
        array $data = [],
        ?OrderRepositoryInterface $orderRepository = null
    ) {
        $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()->get(OrderRepositoryInterface::class);

        parent::__construct($context, $data);
    }

    /**
     * Returns order.
     * Custom email templates are only allowed to use scalar values for variable data.
     * So order is loaded by order_id, that is passed to block from email template.
     * For legacy custom email templates it can pass as an object.
     *
     * @return OrderInterface|null
     * @since 102.1.0
     */
    public function getOrder()
    {
        $order = $this->getData('order');

        if ($order !== null) {
            return $order;
        }
        $orderId = (int) $this->getData('order_id');
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            $this->setData('order', $order);
        }

        return $this->getData('order');
    }

    /**
     * @return array|mixed|null
     */
    public function getActionType()
    {
        return $this->getData('action_type');
    }

    /**
     * Get item row html if item is ready for pickup or dispensed.
     *
     * @param DataObject $item
     *
     * @return  string
     */
    public function getReadyItemHtml(DataObject $item)
    {
        $actionType = $this->getActionType();

        if ($actionType == StatusAction::ACTION_PICKED) {
            $itemStatusJson = $item->getExtensionAttributes()->getWmtItemPickedStatus();

            if ($itemStatusJson) {
                $itemStatuses = json_decode($itemStatusJson, true);
                foreach ($itemStatuses as $itemStatus) {
                    $status = $itemStatus['status'];
                    if ($status == StatusAction::ITEM_STATUS_PICKED) {
                        return $this->getHtmlForItem($itemStatus, $item);
                    }
                }
            }
        }

        if ($actionType == StatusAction::ACTION_DISPENSED) {
            $itemStatusJson = $item->getExtensionAttributes()->getWmtItemDispensedStatus();

            if ($itemStatusJson) {
                $itemStatuses = json_decode($itemStatusJson, true);
                foreach ($itemStatuses as $itemStatus) {
                    $status = $itemStatus['status'];
                    if ($status == StatusAction::ITEM_STATUS_DISPENSED) {
                        return $this->getHtmlForItem($itemStatus, $item);
                    }
                }
            }
        }

        return '';
    }

    /**
     * Get item row html if item is nilpicked or rejected.
     *
     * @param DataObject $item
     *
     * @return  string
     */
    public function getCanceledItemHtml(DataObject $item)
    {
        $actionType = $this->getActionType();

        if ($actionType == StatusAction::ACTION_PICKED) {
            $itemStatusJson = $item->getExtensionAttributes()->getWmtItemPickedStatus();

            if ($itemStatusJson) {
                $itemStatuses = json_decode($itemStatusJson, true);
                foreach ($itemStatuses as $itemStatus) {
                    $status = $itemStatus['status'];
                    if ($status == StatusAction::ITEM_STATUS_NILPICKED) {
                        return $this->getHtmlForItem($itemStatus, $item);
                    }
                }
            }
        }

        if ($actionType == StatusAction::ACTION_DISPENSED) {
            $itemStatusJson = $item->getExtensionAttributes()->getWmtItemDispensedStatus();

            if ($itemStatusJson) {
                $itemStatuses = json_decode($itemStatusJson, true);
                foreach ($itemStatuses as $itemStatus) {
                    $status = $itemStatus['status'];
                    if ($status == StatusAction::ITEM_STATUS_REJECTED) {
                        return $this->getHtmlForItem($itemStatus, $item);
                    }
                }
            }
        }

        return '';
    }

    /**
     * @param array $itemStatus
     * @param DataObject $item
     *
     * @return string
     */
    private function getHtmlForItem(array $itemStatus, DataObject $item): string
    {
        $qtyPickedDispensedOrCanceled = $itemStatus['qty'];
        $item->setQtyPickedDispensedOrCanceled($qtyPickedDispensedOrCanceled);

        return $this->getItemHtml($item);
    }
}
