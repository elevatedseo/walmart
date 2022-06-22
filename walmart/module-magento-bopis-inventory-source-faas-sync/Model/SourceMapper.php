<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceOpeningHoursRepositoryInterface;
use Walmart\BopisInventorySourceApi\Api\InventorySourceParkingSpotRepositoryInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class SourceMapper
{
    private const DEFAULT_COUNTRY_CODE = 'US';
    private const TYPE_IN_STORE_PICKUP = 'INSIDE';
    private const TYPE_CURBSIDE_PICKUP = 'CURBSIDE';

    /**
     * @var InventorySourceOpeningHoursRepositoryInterface
     */
    private InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository;

    /**
     * @var InventorySourceParkingSpotRepositoryInterface
     */
    private InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $regionCollectionFactory;

    /**
     * @param InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository
     * @param InventorySourceParkingSpotRepositoryInterface  $inventorySourceParkingSpotRepository
     * @param CollectionFactory                              $regionCollectionFactory
     */
    public function __construct(
        InventorySourceOpeningHoursRepositoryInterface $inventorySourceOpeningHoursRepository,
        InventorySourceParkingSpotRepositoryInterface $inventorySourceParkingSpotRepository,
        CollectionFactory $regionCollectionFactory
    ) {
        $this->inventorySourceOpeningHoursRepository = $inventorySourceOpeningHoursRepository;
        $this->inventorySourceParkingSpotRepository = $inventorySourceParkingSpotRepository;
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @param SourceInterface $source
     *
     * @return array
     * @throws LocalizedException
     */
    public function get(SourceInterface $source): array
    {
        $parkingSpots = $this->inventorySourceParkingSpotRepository
            ->getListBySourceCode($source->getSourceCode())
            ->getItems();
        $openingHours = $this->inventorySourceOpeningHoursRepository
            ->getListBySourceCode($source->getSourceCode())
            ->getItems();

        $data = [
            'name' => $source->getName(),
            'externalId' => $source->getSourceCode(),
            'address' => [
                'name' => $source->getSourceCode(),
                'street' => $source->getStreet(),
                'city' => $source->getCity(),
                'state' => $this->getRegionCode($source->getRegionId(), $source->getCountryId()),
                'zip' => $source->getPostcode(),
                'countryCode' => $source->getCountryId()
            ],
            'latLong' => [
                'lat' => $source->getLatitude(),
                'lng' => $source->getLongitude()
            ],
            'phone' => [
                'country' => self::DEFAULT_COUNTRY_CODE, // @todo remove hardcoded values
                'countryCode' => 1,
                'number' => $this->formatPhone($source->getPhone())
            ],
            'timeZone' => $source->getExtensionAttributes()->getTimezone(),
            'pickupPointType' => $this->getPickupPointType($source)
        ];

        if ($source->getExtensionAttributes()->getParkingSpotsEnabled() && count($parkingSpots)) {
            $data['parkingBayNumbers'] = implode(
                ',',
                array_map(static fn ($spot) => $spot->getName(), $parkingSpots)
            );
        }

        if (count($openingHours)) {
            $data['operationalHours'] = $this->formatOpeningHours($openingHours);
        }

        return $data;
    }

    /**
     * Find the lowest hour as opening and highest hour as closing
     *
     * @param array $openingHours
     *
     * @return array
     */
    private function formatOpeningHours(array $openingHours): array
    {
        $hours = [];
        foreach ($openingHours as $openingHour) {
            $dayOfWeek = $openingHour->getDayOfWeek();
            $openHour = $this->convertTime($openingHour->getOpenHour());
            $closeHour = $this->convertTime($openingHour->getCloseHour());

            if (!isset($hours[$dayOfWeek])) {
                $hours[$dayOfWeek] = [
                    'start' => $openHour,
                    'end' => $closeHour,
                ];
                continue;
            }

            if ($openHour < $hours[$dayOfWeek]['start']) {
                $hours[$dayOfWeek]['start'] = $openHour;
            }

            if ($closeHour > $hours[$dayOfWeek]['end']) {
                $hours[$dayOfWeek]['end'] = $closeHour;
            }
        }

        return array_map(
            static fn ($hour, $index) =>
            [
                'day' => jddayofweek($index - 1, 1),
                'start' => $hour['start'],
                'end' => $hour['end']
            ],
            $hours,
            array_keys($hours)
        );
    }

    /**
     * @param string|null $phone
     *
     * @return string
     */
    private function formatPhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        return str_replace('+1', '', $phone);
    }

    /**
     * Convert time to format 0000
     *
     * @param string $time
     *
     * @return string
     */
    private function convertTime(string $time): string
    {
        return date('Hi', strtotime($time));
    }

    /**
     * @param int|null $regionId
     *
     * @return string
     */
    private function getRegionCode(?int $regionId, ?string $countryId): string
    {
        if (!$regionId) {
            return '';
        }

        $regionCode = $this->regionCollectionFactory
            ->create()
            ->addFieldToFilter('main_table.region_id', $regionId)
            ->addCountryFilter($countryId)
            ->getFirstItem()
            ->getCode();

        return $regionCode ?: '';
    }

    /**
     * @param SourceInterface $source
     *
     * @return array
     */
    private function getPickupPointType(SourceInterface $source): array
    {
        $types = [];

        if ($source->getExtensionAttributes()->getStorePickupEnabled()) {
            $types[] = self::TYPE_IN_STORE_PICKUP;
        }

        if ($source->getExtensionAttributes()->getCurbsideEnabled()) {
            $types[] = self::TYPE_CURBSIDE_PICKUP;
        }

        return $types;
    }
}
