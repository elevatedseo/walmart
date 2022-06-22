<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOperationQueue\Model\Config\Queue;

use Magento\Framework\Data\OptionSourceInterface;
use Walmart\BopisOperationQueueApi\Api\OperationTypeInterface;

class OperationType implements OptionSourceInterface
{
    /**
     * Retrieve Operation Type array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => OperationTypeInterface::NEW_ORDER, 'label' => __('New Order')],
            ['value' => OperationTypeInterface::CANCEL_ORDER, 'label' => __('Cancel Order')],
        ];
    }
}
