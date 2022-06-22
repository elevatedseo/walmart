<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Check is Order is Ship To Store
 */
class IsShipToStore
{
    /**
     * Notification is not required - order doesn't have Ship To Store items
     */
    public const EMAIL_STATUS_NOT_REQUIRED = 0;

    /**
     * Notification is required for order - Ship To Store items exists
     */
    public const EMAIL_STATUS_PENDING = 2;

    /**
     * Admin user has been notified
     */
    public const EMAIL_STATUS_SENT = 4;

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function execute(OrderInterface $order): bool
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            return false;
        }

        return (bool)$extensionAttributes->getWmtIsShipToStore();
    }
}
