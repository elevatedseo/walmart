<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\Source;

use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisInventorySourceAdminUi\Model\Config\Source\OpeningHours\DayOfWeek;
use Magento\Framework\Api\SearchResults;

class Renderer
{
    /**
     * @var CountryFactory
     */
    private CountryFactory $countryFactory;

    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;

    /**
     * @var DayOfWeek
     */
    private DayOfWeek $dayOfWeek;

    /**
     * Renderer constructor.
     *
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     * @param DayOfWeek $dayOfWeek
     */
    public function __construct(
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        DayOfWeek $dayOfWeek
    ) {
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @param string $countryCode
     *
     * @return string
     */
    private function getCountryName(string $countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);

        return $country->getName();
    }

    /**
     * @param SourceInterface $source
     *
     * @return string
     */
    private function getRegion(SourceInterface $source)
    {
        if ($source->getRegionId()) {
            $region = $this->regionFactory->create()->load($source->getRegionId());
            return $region->getCode();
        }

        return $source->getRegion();
    }

    /**
     * @param SourceInterface $source
     *
     * @return string
     */
    public function getFormattedStoreAddressHtml(SourceInterface $source)
    {
        $addressHtml = "";

        $addressHtml .= "{$source->getExtensionAttributes()->getFrontendName()}<br>";
        $addressHtml .= "{$source->getStreet()}<br>";
        $addressHtml .=
            "{$source->getCity()}, {$this->getRegion($source)} {$source->getPostcode()}<br>";
        $addressHtml .= "{$this->getCountryName($source->getCountryId())}<br>";

        $addressHtml .= __("Phone: ") . "{$source->getPhone()}";

        return $addressHtml;
    }

    /**
     * @param SearchResults $openingHours
     *
     * @return string
     */
    public function getFormattedOpeningHoursHtml(SearchResults $openingHours)
    {
        $openingHoursHtml = "<span>" . __("Store hours") . "</span><br>";

        foreach ($openingHours->getItems() as $item) {
            $dayOfWeek = $this->dayOfWeek->getOptionLabel($item->getDayOfWeek());
            $openHour = strtoupper($item->getOpenHour());
            $closeHour = strtoupper($item->getCloseHour());
            $openingHoursHtml .= "{$dayOfWeek}: {$openHour} - {$closeHour}<br>";
        }

        return $openingHoursHtml;
    }
}
