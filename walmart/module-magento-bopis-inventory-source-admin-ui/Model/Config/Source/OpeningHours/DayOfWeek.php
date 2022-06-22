<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceAdminUi\Model\Config\Source\OpeningHours;

use Magento\Framework\Data\OptionSourceInterface;

class DayOfWeek implements OptionSourceInterface
{
    const DAY_OF_WEEK_SUNDAY = 0;
    const DAY_OF_WEEK_MONDAY = 1;
    const DAY_OF_WEEK_TUESDAY = 2;
    const DAY_OF_WEEK_WEDNESDAY = 3;
    const DAY_OF_WEEK_THURSDAY = 4;
    const DAY_OF_WEEK_FRIDAY = 5;
    const DAY_OF_WEEK_SATURDAY = 6;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DAY_OF_WEEK_SUNDAY, 'label' => __('Sunday')],
            ['value' => self::DAY_OF_WEEK_MONDAY, 'label' => __('Monday')],
            ['value' => self::DAY_OF_WEEK_TUESDAY, 'label' => __('Tuesday')],
            ['value' => self::DAY_OF_WEEK_WEDNESDAY, 'label' => __('Wednesday')],
            ['value' => self::DAY_OF_WEEK_THURSDAY, 'label' => __('Thursday')],
            ['value' => self::DAY_OF_WEEK_FRIDAY, 'label' => __('Friday')],
            ['value' => self::DAY_OF_WEEK_SATURDAY, 'label' => __('Saturday')]
        ];
    }

    /**
     * @param $day
     *
     * @return mixed|string
     */
    public function getOptionLabel($day)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $day) {
                return $option['label'];
            }
        }

        return '';
    }
}
