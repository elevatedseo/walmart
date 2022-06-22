<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\InventoryInStorePickupShippingApi\Model;

use Magento\InventoryInStorePickupShippingApi\Model\IsInStorePickupDeliveryCartInterface;
use Magento\Quote\Api\Data\CartInterface;

class IsInStorePickupDeliveryCartPlugin
{
    /**
     * @param IsInStorePickupDeliveryCartInterface $subject
     * @param bool $result
     * @param CartInterface $cart
     *
     * @return bool
     */
    public function afterExecute(
        IsInStorePickupDeliveryCartInterface $subject,
        bool $result,
        CartInterface $cart
    ): bool {
        if ($cart->getIsVirtual()) {
            return false;
        }

        return $result;
    }
}
