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
 * LIst of 2FA providers
 */
class Providers implements OptionSourceInterface
{
    /**#@+
     * Display types constants
     */
    public const PROVIDER_GOOGLE_CODE = 'google';
    public const PROVIDER_GOOGLE_NAME = 'Google';
    /**#@-*/

    /**
     * @inheirtDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::PROVIDER_GOOGLE_CODE, 'label' => __(self::PROVIDER_GOOGLE_NAME)]
        ];
    }
}
