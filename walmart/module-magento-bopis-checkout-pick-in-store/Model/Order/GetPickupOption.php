<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Extracts pickup_option from the order entity.
 */
class GetPickupOption
{
    /**
     * Extract pickup option from order.
     *
     * @param OrderInterface $order
     * @return string|null
     */
    public function execute(OrderInterface $order): ?string
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes) {
            return $extensionAttributes->getPickupOption();
        }

        return null;
    }
}
