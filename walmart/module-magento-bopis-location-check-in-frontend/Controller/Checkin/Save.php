<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInFrontend\Controller\Checkin;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisLocationCheckIn\Api\CheckInHashProviderInterface;
use Exception;
use Walmart\BopisLocationCheckInApi\Api\CheckInRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisLogging\Logger\Logger;

class Save implements HttpPostActionInterface
{
    private RequestInterface $request;

    private CheckInHashProviderInterface $checkInHashProvider;

    private CheckInRepositoryInterface $checkInRepository;

    private OrderRepositoryInterface $orderRepository;

    private ResultFactory $resultFactory;

    private ManagerInterface $messageManager;

    private GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId;

    private Logger $logger;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        CheckInHashProviderInterface $checkInHashProvider,
        CheckInRepositoryInterface $checkInRepository,
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $messageManager,
        GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId,
        Logger $logger
    ) {
        $this->request = $request;
        $this->checkInHashProvider = $checkInHashProvider;
        $this->checkInRepository = $checkInRepository;
        $this->orderRepository = $orderRepository;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->getPickupLocationCodeByOrderId = $getPickupLocationCodeByOrderId;
        $this->logger = $logger;
    }

    public function execute()
    {
        $orderId = $this->request->getParam('order_id');
        $hash = $this->request->getParam('hash');

        try {
            if (!$orderId || !$hash) {
                return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
            }

            if (!$this->checkInHashProvider->isValid($hash, (int)$orderId)) {
                return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
            }

            $checkIn = $this->getCheckIn((int)$orderId);
            if (empty($checkIn->getCheckInId())) {
                $checkIn->setStatus(CheckInInterface::STATUS_STARTED);
            }
            $checkIn->addData($this->prepareData());

            $this->checkInRepository->save($checkIn);
        } catch (Exception $exception) {
            $this->logger->error(__('There was a problem with saving the check-in', [
                'msg' => $exception->getMessage()
            ]));

            $this->messageManager->addErrorMessage(__(
                'There was a problem with saving the check-in. Attempt to check-in again in a few minutes.'
            ));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('sales/checkin/index', [
            'order_id' => $orderId,
            'hash' => $hash
        ]);
        return $resultRedirect;
    }

    /**
     * @param int $orderId
     *
     * @return CheckInInterface
     */
    private function getCheckIn(int $orderId): CheckInInterface
    {
        try {
            return $this->checkInRepository->getByOrderId($orderId);
        } catch (NoSuchEntityException $exception) {
            return $this->checkInRepository->create();
        }
    }

    /**
     * @return array
     */
    private function prepareData(): array
    {
        $data = $this->request->getParams();
        $data[CheckInInterface::EXTERNAL_ID] = $data['hash'];
        $data[CheckInInterface::CUSTOMER_ID] = $this->getOrder((int)$data['order_id'])->getCustomerId();
        $data[CheckInInterface::SOURCE_CODE] = $this->getPickupLocationCodeByOrderId->execute((int)$data['order_id']);

        if (!empty($data['other_parking_spot'])) {
            $data[CheckInInterface::PARKING_SPOT] = $data['other_parking_spot'];
        }

        unset($data['form_key']);

        return $data;
    }

    /**
     * @param int $orderId
     *
     * @return OrderInterface
     */
    private function getOrder(int $orderId): OrderInterface
    {
        return $this->orderRepository->get($orderId);
    }
}
