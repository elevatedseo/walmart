<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Plugin\InventoryAdminUi\DataProvider;

use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceParkingSpotRepositoryInterface;

class LoadDynamicRowsPlugin
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
     * @var Config
     */
    private Config $config;

    /**
     * @param InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository
     * @param InventorySourceParkingSpotRepositoryInterface  $inventorySourceParkingSpotRepository
     * @param Config                                         $config
     */
    public function __construct(
        InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository,
        InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository,
        Config $config
    ) {
        $this->inventorySourceOpeningHoursRepository = $inventorySourceOpeningHoursRepository;
        $this->inventorySourceParkingSpotRepository = $inventorySourceParkingSpotRepository;
        $this->config = $config;
    }

    /**
     * Load dynamic rows of opening hours and parking spots
     *
     * @param SourceDataProvider $subject
     * @param $result
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetData(SourceDataProvider $subject, $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($sourceCode = array_key_first($result)) {
            if (array_key_exists('general', $result[$sourceCode])) {
                //load opening hours
                $openingHours = $this->inventorySourceOpeningHoursRepository->getListBySourceCode((string) $sourceCode);
                foreach ($openingHours->getItems() as $item) {
                    $result[$sourceCode]['general']['inventory_source_opening_hours_dynamic_rows'][] = [
                        'source_code'          => $sourceCode,
                        'source_open_hours_id' => $item->getSourceOpenHoursId(),
                        'day_of_week'          => $item->getDayOfWeek(),
                        'openhour'             => $item->getOpenHour(),
                        'closehour'            => $item->getCloseHour()
                    ];
                }
                //load parking spots
                $parkingSpots = $this->inventorySourceParkingSpotRepository->getListBySourceCode((string) $sourceCode);
                foreach ($parkingSpots->getItems() as $item) {
                    $result[$sourceCode]['general']['inventory_source_parking_spot_dynamic_rows'][] = [
                        'parking_spot_enabled' => $item->getEnabled(),
                        'parking_spot_name'    => $item->getName()
                    ];
                }
            }
        }

        return $result;
    }
}
