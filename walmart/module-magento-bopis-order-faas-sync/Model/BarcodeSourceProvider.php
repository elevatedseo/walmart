<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisOrderFaasSync\Model\Config\Source\BarcodeType;

class BarcodeSourceProvider
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     *
     * @return array
     */
    public function get(OrderInterface $order, OrderItemInterface $item): array
    {
        $attributeCode = $this->configuration->getBarcodeSource();
        $product = $item->getProduct();

        if ($product === null) {
            $order->addCommentToStatusHistory(__("Product with SKU %1 doesn't exist", $item->getSku()));
            $this->orderRepository->save($order);

            return [BarcodeType::SKU, $item->getSku()];
        }

        $barcode = $product->getData($attributeCode);
        if (empty($barcode)) {
            $order->addCommentToStatusHistory(__('Product with SKU %1 is missing Barcode value.', $item->getSku()));
            $this->orderRepository->save($order);

            return [BarcodeType::SKU, $item->getSku()];
        }

        return [$this->configuration->getBarcodeType(), $barcode];
    }
}
