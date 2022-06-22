<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOperationQueue\Model\Config\Queue;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const NOT_SENT = 0;
    public const SENT = 1;
    public const FAILED = 2;
    public const COMPLETED = 3;
    public const CANCELED = 4;

    /**
     * Retrieve Queue Status array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NOT_SENT, 'label' => __('Not Sent')],
            ['value' => self::SENT, 'label' => __('Sent')],
            ['value' => self::FAILED, 'label' => __('Failed')],
            ['value' => self::COMPLETED, 'label' => __('Completed')],
            ['value' => self::CANCELED, 'label' => __('Canceled')],
        ];
    }
}
