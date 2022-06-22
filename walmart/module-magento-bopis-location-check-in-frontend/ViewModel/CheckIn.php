<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInFrontend\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceParkingSpotRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\CheckInRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisInventorySourceApi\Model\InventorySource;

class CheckIn implements ArgumentInterface
{
    const PICKUP_OPTION_CURBSIDE = 'curbside';

    private CarColorRepositoryInterface $carColorRepository;

    private CarMakeRepositoryInterface $carMakeRepository;

    private CheckInRepositoryInterface $checkInRepository;

    private RequestInterface $request;

    private UrlInterface $url;

    private SourceRepositoryInterface $sourceRepository;

    private InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository;

    private OrderRepositoryInterface $orderRepository;

    private InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository;

    private ?CheckInInterface $checkIn = null;

    private ?SourceInterface $source = null;

    private ?OrderInterface $order = null;

    private GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId;

    /**
     * @var InventorySource
     */
    private InventorySource $inventorySource;

    /**
     * @param CarColorRepositoryInterface                    $carColorRepository
     * @param CarMakeRepositoryInterface                     $carMakeRepository
     * @param CheckInRepositoryInterface                     $checkInRepository
     * @param RequestInterface                               $request
     * @param UrlInterface                                   $url
     * @param SourceRepositoryInterface                      $sourceRepository
     * @param InventorySourceParkingSpotRepositoryInterface  $inventorySourceParkingSpotRepository
     * @param InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository
     * @param OrderRepositoryInterface                       $orderRepository
     * @param GetPickupLocationCodeByOrderId                 $getPickupLocationCodeByOrderId
     * @param InventorySource                                $inventorySource
     */
    public function __construct(
        CarColorRepositoryInterface $carColorRepository,
        CarMakeRepositoryInterface $carMakeRepository,
        CheckInRepositoryInterface $checkInRepository,
        RequestInterface $request,
        UrlInterface $url,
        SourceRepositoryInterface $sourceRepository,
        InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository,
        InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository,
        OrderRepositoryInterface $orderRepository,
        GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId,
        InventorySource $inventorySource
    ) {
        $this->carColorRepository = $carColorRepository;
        $this->carMakeRepository = $carMakeRepository;
        $this->checkInRepository = $checkInRepository;
        $this->request = $request;
        $this->url = $url;
        $this->sourceRepository = $sourceRepository;
        $this->inventorySourceParkingSpotRepository = $inventorySourceParkingSpotRepository;
        $this->orderRepository = $orderRepository;
        $this->inventorySourceOpeningHoursRepository = $inventorySourceOpeningHoursRepository;
        $this->getPickupLocationCodeByOrderId = $getPickupLocationCodeByOrderId;
        $this->inventorySource = $inventorySource;
    }

    /**
     * @return array
     */
    public function getColors(): array
    {
        $colors = [];

        foreach ($this->carColorRepository->getList()->getItems() as $color) {
            $colors[] = [
                'text' => $color->getValue(),
                'value' => $color->getCarColorId()
            ];
        }

        return $colors;
    }

    /**
     * @return string[]
     */
    public function getCarMakes(): array
    {
        $carMakes = [];
        foreach ($this->carMakeRepository->getList()->getItems() as $carMake) {
            $carMakes[] = [
                'text' => $carMake->getValue(),
                'value' => $carMake->getCarMakeId(),
            ];
        }

        return $carMakes;
    }

    /**
     * @return bool
     */
    public function locationHasCords(): bool
    {
        if ($this->getPickupLocation()->getLatitude() && $this->getPickupLocation()->getLongitude()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCheckinActionUrl(): string
    {
        return $this->url->getUrl('sales/checkin/save', [
            'order_id' => $this->getOrderId(),
            'hash' => $this->request->getParam('hash')
        ]);
    }

    /**
     * @return string
     */
    public function getMapUrl(): string
    {
        if ($this->locationHasCords()) {
            return sprintf(
                'https://maps.google.com/?q=%s,%s',
                $this->getPickupLocation()->getLatitude(),
                $this->getPickupLocation()->getLongitude()
            );
        }

        return '';
    }

    /**
     * @return SourceInterface|null
     */
    public function getPickupLocation(): ?SourceInterface
    {
        if ($this->source === null) {
            try {
                $sourceCode = $this->getPickupLocationCodeByOrderId->execute($this->getOrderId());
                if (empty($sourceCode)) {
                    return null;
                }

                $this->source = $this->sourceRepository->get($sourceCode);
            } catch (NoSuchEntityException $exception) {
                return null;
            }
        }

        return $this->source;
    }

    public function getInstructions(): ?string
    {
        $order = $this->getOrder();
        $source = $this->getPickupLocation();

        if ($this->isCurbside()) {
            return $this->inventorySource->getCurbsideInstructions($source, (int) $order->getStore()->getWebsiteId());
        }

        return $this->inventorySource->getStorePickupInstructions($source, (int) $order->getStore()->getWebsiteId());
    }

    /**
     * @return string[]
     * @throws LocalizedException
     */
    public function getParkingSpots(): array
    {
        if (!$this->getPickupLocation()) {
            return [];
        }

        if (!$this->getPickupLocation()->getExtensionAttributes()->getParkingSpotsEnabled()) {
            return [];
        }

        $options = [];
        $parkingSpots = $this->inventorySourceParkingSpotRepository->getListBySourceCode(
            $this->getPickupLocation()->getSourceCode()
        )->getItems();

        foreach ($parkingSpots as $parkingSpot) {
            $options[] = [
                'value' => $parkingSpot->getName(),
                'text' => $parkingSpot->getName()
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getOpenHours(): array
    {
        if (!$this->getPickupLocation()) {
            return [];
        }

        return $this->inventorySourceOpeningHoursRepository->getListBySourceCode(
            $this->getPickupLocation()->getSourceCode()
        )->getItems();
    }

    /**
     * @return string
     */
    public function getSavedParkingSpot(): string
    {
        if ($this->getCheckIn() && $this->getCheckIn()->getParkingSpot()) {
            return $this->getCheckIn()->getParkingSpot();
        }

        return '';
    }

    /**
     * @return int|null
     */
    public function getSavedColor(): ?int
    {
        if ($this->getCheckIn() && $this->getCheckIn()->getCarColor()) {
            return $this->getCheckIn()->getCarColor();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getSavedColorLabel(): ?string
    {
        try {
            if ($this->getCheckIn() && $this->getCheckIn()->getCarColor()) {
                return $this->carColorRepository->get($this->getCheckIn()->getCarColor())->getValue();
            }
            return null;
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function getSavedMake(): ?int
    {
        if ($this->getCheckIn()) {
            $this->getCheckIn()->getCarMake();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getSavedMakeLabel(): ?string
    {
        try {
            if ($this->getCheckIn() && $this->getCheckIn()->getCarMake()) {
                return $this->carMakeRepository->get($this->getCheckIn()->getCarMake())->getValue();
            }

            return null;
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getSavedMessage(): string
    {
        if ($this->getCheckIn() && $this->getCheckIn()->getAdditionalNotes()) {
            return $this->getCheckIn()->getAdditionalNotes();
        }

        return '';
    }

    /**
     * @return CheckInInterface|null
     */
    public function getCheckIn(): ?CheckInInterface
    {
        if ($this->checkIn === null) {
            try {
                $this->checkIn = $this->checkInRepository->getByOrderId((int)$this->request->getParam('order_id'));
            } catch (NoSuchEntityException $exception) {
                return null;
            }
        }

        return $this->checkIn;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isCurbside(): bool
    {
        return $this->getOrder()->getExtensionAttributes()->getPickupOption() == self::PICKUP_OPTION_CURBSIDE;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function canAddCustomParkingSpot(): bool
    {
        if (!$this->getPickupLocation()) {
            return false;
        }

        return (bool)$this->getPickupLocation()->getExtensionAttributes()->getCustomParkingSpotEnabled();
    }

    /**
     * @return string|null
     */
    public function getOrderNumber(): string
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * @param int $dayOfWeek
     *
     * @return string
     */
    public function getDayOfWeekName(int $dayOfWeek): string
    {
        return date('D', strtotime("Sunday +{$dayOfWeek} days"));
    }

    /**
     * @return bool
     */
    public function isCheckInStarted(): bool
    {
        return $this->getCheckIn() && $this->getCheckIn()->getStatus() === CheckInInterface::STATUS_STARTED;
    }

    /**
     * @return bool
     */
    public function isCheckInFinished(): bool
    {
        return $this->getCheckIn() && $this->getCheckIn()->getStatus() === CheckInInterface::STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function isCarColorEnabled(): bool
    {
        $location = $this->getPickupLocation();

        return $location ? $location->getExtensionAttributes()->getUseCarColor() : false;
    }

    /**
     * @return bool
     */
    public function isCarColorMandatory(): bool
    {
        $location = $this->getPickupLocation();

        return $location && $this->isCarColorEnabled()
            ? $location->getExtensionAttributes()->getCarColorMandatory()
            : false;
    }

    /**
     * @return bool
     */
    public function isCarMakeEnabled(): bool
    {
        $location = $this->getPickupLocation();

        return $location ? $location->getExtensionAttributes()->getUseCarMake() : false;
    }

    /**
     * @return bool
     */
    public function isCarMakeMandatory(): bool
    {
        $location = $this->getPickupLocation();

        return $location && $this->isCarMakeEnabled()
            ? $location->getExtensionAttributes()->getCarMakeMandatory()
            : false;
    }

    /**
     * @return bool
     */
    public function isAdditionalInfoEnabled(): bool
    {
        $location = $this->getPickupLocation();

        return $location ? $location->getExtensionAttributes()->getUseAdditionalInformation() : false;
    }

    /**
     * @return bool
     */
    public function isAdditionalInfoMandatory(): bool
    {
        $location = $this->getPickupLocation();

        return $location && $this->isAdditionalInfoEnabled()
            ? $location->getExtensionAttributes()->getAdditionalInformationMandatory()
            : false;
    }

    /**
     * @return OrderInterface
     */
    private function getOrder(): OrderInterface
    {
        if ($this->order === null) {
            $this->order = $this->orderRepository->get($this->getOrderId());
        }

        return $this->order;
    }

    /**
     * @return int
     */
    private function getOrderId(): int
    {
        return (int)$this->request->getParam(CheckInInterface::ORDER_ID);
    }
}
