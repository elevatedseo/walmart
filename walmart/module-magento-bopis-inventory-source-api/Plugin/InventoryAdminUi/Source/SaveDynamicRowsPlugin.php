<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Plugin\InventoryAdminUi\Source;

use Magento\Framework\Controller\ResultInterface;
use Magento\InventoryAdminUi\Controller\Adminhtml\Source\Save;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceParkingSpotRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceOpeningHoursInterfaceFactory;
use Walmart\BopisInventorySourceApi\Api\Data\InventorySourceParkingSpotInterfaceFactory;

class SaveDynamicRowsPlugin
{
    /**
     * @var InventorySourceOpeningHoursRepositoryInterface
     */
    private $inventorySourceOpeningHoursRepository;

    /**
     * @var InventorySourceParkingSpotRepositoryInterface
     */
    private $inventorySourceParkingSpotRepository;

    /**
     * @var InventorySourceOpeningHoursInterfaceFactory
     */
    private $openingHoursInterfaceFactory;

    /**
     * @var InventorySourceParkingSpotInterfaceFactory
     */
    private $parkingSpotInterfaceFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository
     * @param InventorySourceParkingSpotRepositoryInterface  $inventorySourceParkingSpotRepository
     * @param InventorySourceOpeningHoursInterfaceFactory    $openingHoursInterfaceFactory
     * @param InventorySourceParkingSpotInterfaceFactory     $parkingSpotInterfaceFactory
     * @param Config                                         $config
     */
    public function __construct(
        InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository,
        InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository,
        InventorySourceOpeningHoursInterfaceFactory $openingHoursInterfaceFactory,
        InventorySourceParkingSpotInterfaceFactory $parkingSpotInterfaceFactory,
        Config $config
    ) {
        $this->inventorySourceOpeningHoursRepository = $inventorySourceOpeningHoursRepository;
        $this->inventorySourceParkingSpotRepository = $inventorySourceParkingSpotRepository;
        $this->openingHoursInterfaceFactory = $openingHoursInterfaceFactory;
        $this->parkingSpotInterfaceFactory = $parkingSpotInterfaceFactory;
        $this->config = $config;
    }

    /**
     * Save dynamic rows of opening hours and parking spots
     *
     * @param Save            $subject
     * @param ResultInterface $result
     *
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterExecute(Save $subject, ResultInterface $result): ResultInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $formData = $subject->getRequest()->getParam('general');
        $sourceCode = $formData['source_code'];

        //save opening hours
        $this->inventorySourceOpeningHoursRepository->deleteAllBySourceCode($sourceCode);
        if (array_key_exists('inventory_source_opening_hours_dynamic_rows', $formData)) {
            $openingHoursData = $formData['inventory_source_opening_hours_dynamic_rows'];
            foreach ($openingHoursData as $row) {
                $pickupOpeningHours = $this->openingHoursInterfaceFactory->create();
                $pickupOpeningHours->setSourceCode($sourceCode);
                $pickupOpeningHours->setDayOfWeek((int) $row['day_of_week']);
                $pickupOpeningHours->setOpenHour($row['openhour']);
                $pickupOpeningHours->setCloseHour($row['closehour']);

                $this->inventorySourceOpeningHoursRepository->save($pickupOpeningHours);
            }
        }
        //save parking spots
        $this->inventorySourceParkingSpotRepository->deleteAllBySourceCode($sourceCode);
        if (array_key_exists('inventory_source_parking_spot_dynamic_rows', $formData)) {
            $parkingSpotData = $formData['inventory_source_parking_spot_dynamic_rows'];

            foreach ($parkingSpotData as $row) {
                $parkingSpot = $this->parkingSpotInterfaceFactory->create();
                $parkingSpot->setSourceCode($sourceCode);
                $parkingSpot->setEnabled((boolean) $row['parking_spot_enabled']);
                $parkingSpot->setName($row['parking_spot_name']);

                $this->inventorySourceParkingSpotRepository->save($parkingSpot);
            }
        }

        return $result;
    }
}
