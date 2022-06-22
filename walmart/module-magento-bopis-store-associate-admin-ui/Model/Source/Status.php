<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Constants
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    /**#@-*/

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        return [
            ['value' => self::STATUS_ACTIVE, 'label' => __('Active')],
            ['value' => self::STATUS_INACTIVE, 'label' => __('Inactive')]
        ];
    }
}
