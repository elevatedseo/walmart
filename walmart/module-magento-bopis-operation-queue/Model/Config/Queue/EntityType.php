<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOperationQueue\Model\Config\Queue;

use Magento\Framework\Data\OptionSourceInterface;

class EntityType implements OptionSourceInterface
{
    public const ENTITY_TYPE_ORDER = 'order';

    /**
     * Retrieve Entity Type array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ENTITY_TYPE_ORDER, 'label' => __('Order')]
        ];
    }
}
