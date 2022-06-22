<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisOrderUpdate\Block\Order\Totals;

use Magento\Framework\View\Element\AbstractBlock;

class TotalRefunded extends AbstractBlock
{
    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();

        $orderTotalsBlock->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code'   => 'total_refunded',
                    'label'  => __('Total Refunded'),
                    'value'  => $order->getTotalRefunded(),
                    'strong' => true,
                ]
            ),
            'grand_total'
        );
    }
}
