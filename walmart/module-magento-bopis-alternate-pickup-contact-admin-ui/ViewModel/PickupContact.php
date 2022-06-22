<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContactAdminUi\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class PickupContact implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @param RequestInterface $request
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return OrderAddressInterface|null
     */
    public function getPickupContact(): ?OrderAddressInterface
    {
        $order = $this->getOrder();
        if (!$order) {
            return null;
        }

        return $order->getExtensionAttributes()->getPickupContact();
    }

    /**
     * @return OrderInterface|null
     */
    private function getOrder(): ?OrderInterface
    {
        try {
            return $this->orderRepository->get($this->request->getParam('order_id'));
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }
}
