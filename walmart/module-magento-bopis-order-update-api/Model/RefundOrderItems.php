<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Magento\Framework\Exception\ValidatorException;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\RefundInvoiceInterface;
use Magento\Sales\Api\RefundOrderInterface;

class RefundOrderItems
{
    /**
     * @var CreditmemoItemCreationInterfaceFactory
     */
    private CreditmemoItemCreationInterfaceFactory $creditmemoItemCreation;

    /**
     * @var RefundOrderInterface
     */
    private RefundOrderInterface $refundOrder;

    private RefundInvoiceInterface $refundInvoice;

    /**
     * @param RefundOrderInterface $refundOrder
     * @param RefundInvoiceInterface $refundInvoice
     * @param CreditmemoItemCreationInterfaceFactory $creditmemoItemCreation
     */
    public function __construct(
        RefundOrderInterface $refundOrder,
        RefundInvoiceInterface $refundInvoice,
        CreditmemoItemCreationInterfaceFactory $creditmemoItemCreation
    ) {
        $this->refundOrder = $refundOrder;
        $this->creditmemoItemCreation = $creditmemoItemCreation;
        $this->refundInvoice = $refundInvoice;
    }

    /**
     * Refund order items if they are invoiced.
     *
     * @param  $orderId
     * @param  $invoices
     * @param  $itemsToRefund
     *
     * @throws ValidatorException
     */
    public function execute($orderId, $invoices, $itemsToRefund): void
    {
        foreach ($invoices as $invoice) {
            $items = [];
            $invoiceItems = $invoice->getItems();

            foreach ($invoiceItems as $invoiceItem) {
                $orderItemId = $invoiceItem->getOrderItemId();
                foreach ($itemsToRefund as $itemToRefund) {
                    $itemIdToRefund = $itemToRefund['item_id'];
                    if ((int)$itemIdToRefund !== (int)$orderItemId) {
                        continue;
                    }

                    $qtyInvoiced = $invoiceItem->getQty();
                    $qtyToRefund = (int) $itemToRefund['qty_to_refund'];

                    if ((int) $qtyInvoiced >= $qtyToRefund) {
                        $creditmemoItemCreation = $this->creditmemoItemCreation->create();
                        $items[] = $creditmemoItemCreation
                            ->setQty($qtyToRefund)
                            ->setOrderItemId($orderItemId);
                        continue;
                    }

                    throw new ValidatorException(__("Item '%1' can't be refunded", $itemIdToRefund));
                }
            }

            if (count($items) === 0) {
                continue;
            }

            if ($this->canRefundOnline($invoice)) {
                $this->refundInvoice->execute($invoice->getId(), $items, true, true, false);
                continue;
            }

            $this->refundOrder->execute($orderId, $items, true, false);
        }
    }

    /**
     * @param InvoiceInterface $invoice
     *
     * @return bool
     * @see \Magento\Sales\Block\Adminhtml\Order\Invoice\View::_construct
     */
    private function canRefundOnline(InvoiceInterface $invoice): bool
    {
        $orderPayment = $invoice->getOrder()->getPayment();

        return (
                $orderPayment->canRefundPartialPerInvoice()
                && $invoice->canRefund()
                && $orderPayment->getAmountPaid() > $orderPayment->getAmountRefunded()
           ) || (
               $orderPayment->canRefund()
               && !$invoice->getIsUsedForRefund()
            );
    }
}
