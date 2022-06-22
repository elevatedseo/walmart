<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisOrderUpdate\Block\Order\Email\Items\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder as DefaultOrderAlias;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

class PartlyCanceled extends DefaultOrderAlias
{
    /**
     * Get the html for item price
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return string
     * @throws LocalizedException
     */
    public function getItemPrice(OrderItem $item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $item->setTotalPickedDispensedOrCanceled((float) $item->getPrice() * (float) $this->getItem()->getQtyPickedDispensedOrCanceled());

        $block->setItem($item);
        return $block->toHtml();
    }
}
