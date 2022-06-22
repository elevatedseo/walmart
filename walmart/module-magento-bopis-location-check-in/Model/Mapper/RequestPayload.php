<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model\Mapper;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisLocationCheckIn\Api\Mapper\CheckInToChaasInterface;
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

class RequestPayload implements CheckInToChaasInterface
{
    private const ORDER_TYPE = 'SO';
    private const CHECKIN_TYPE_WEB = 'Web';
    private const CHECKIN_DEVICE_TYPE_IOS = 'iOS';
    private const CHECKIN_FULFILLMENT_TYPE_PICKUP = 'Pickup';

    /**
     * @var CarColorRepositoryInterface
     */
    private CarColorRepositoryInterface $carColorRepository;

    /**
     * @var CarMakeRepositoryInterface
     */
    private CarMakeRepositoryInterface $carMakeRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param CarColorRepositoryInterface $carColorRepository
     * @param CarMakeRepositoryInterface  $carMakeRepository
     * @param OrderRepositoryInterface    $orderRepository
     * @param SourceRepositoryInterface   $sourceRepository
     * @param Config                      $config
     */
    public function __construct(
        CarColorRepositoryInterface $carColorRepository,
        CarMakeRepositoryInterface $carMakeRepository,
        OrderRepositoryInterface $orderRepository,
        SourceRepositoryInterface $sourceRepository,
        Config $config
    ) {
        $this->carColorRepository = $carColorRepository;
        $this->carMakeRepository = $carMakeRepository;
        $this->orderRepository = $orderRepository;
        $this->sourceRepository = $sourceRepository;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function map(CheckInInterface $checkIn): array
    {
        $order = $this->orderRepository->get($checkIn->getOrderId());
        $color = null;
        $make = null;
        $source = $this->sourceRepository->get($checkIn->getSourceCode());
        $isGuestOrder = $order->getCustomerIsGuest();

        if ($checkIn->getCarColor()) {
            $color = $this->carColorRepository->get($checkIn->getCarColor());
        }
        if ($checkIn->getCarMake()) {
            $make = $this->carMakeRepository->get($checkIn->getCarMake());
        }

        return [
            'payload' => [
                'clientId' => $this->config->getEnvClientId(),
                'customerInfo' => [
                    'customerId' => $isGuestOrder ? 'Guest' : $order->getCustomerId(),
                    'firstname' => $isGuestOrder ? 'Guest' : $order->getCustomerFirstname(),
                    'lastname' => $isGuestOrder ? 'Guest' : $order->getCustomerLastname(),
                    'phone' => $order->getBillingAddress()->getTelephone(),
                ],
                'checkInType' => self::CHECKIN_TYPE_WEB,
                'deviceType' => self::CHECKIN_DEVICE_TYPE_IOS,
                'fulfillmentType' => self::CHECKIN_FULFILLMENT_TYPE_PICKUP,
                'etaInSeconds' => 0,
                'pickupPointInfo' => [
                    'pickupPointId' => $source->getId(),
                    'storeId' => $source->getId()
                ],
                'parkingInfo' => [
                    'vehicleColor' => $color ? $color->getValue() : '',
                    'vehicleMake' => $make ? $make->getValue() : '',
                    'parkBayNumber' => $checkIn->getParkingSpot(),
                    'parkingNote' => $checkIn->getAdditionalNotes()
                ],
                'orders' => [
                    [
                        'orderNumber' => $order->getIncrementId(),
                        'type' => self::ORDER_TYPE
                    ]
                ]
            ]
        ];
    }
}
