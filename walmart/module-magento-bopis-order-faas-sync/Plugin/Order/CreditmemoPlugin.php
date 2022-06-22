<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Order;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class CreditmemoPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Creditmemo $subject
     * @param bool $result
     *
     * @return bool
     */
    public function afterCanRefund(
        Creditmemo $subject,
        bool $result
    ): bool {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($subject->getOrder()->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return $result;
        }

        if ($subject->getOrder()->getStatus() === Order::STATE_CLOSED) {
            return false;
        }

        return $result;
    }
}
