<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * LIst of TFA policy options
 */
class TfaPolicyOptions implements OptionSourceInterface
{
    /**#@+
     * Display types constants
     */
    public const OPTIONAL_OPTION_CODE = 'optional';
    public const OPTIONAL_OPTION_LABEL = 'Optional';
    public const MANDATORY_OPTION_CODE = 'mandatory';
    public const MANDATORY_OPTION_LABEL = 'Mandatory';
    /**#@-*/

    /**
     * @inheirtDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::OPTIONAL_OPTION_CODE, 'label' => __(self::OPTIONAL_OPTION_LABEL)],
            ['value' => self::MANDATORY_OPTION_CODE, 'label' => __(self::MANDATORY_OPTION_LABEL)]
        ];
    }
}
