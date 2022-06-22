<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BarcodeType implements OptionSourceInterface
{
    public const SKU = 'SKU';
    public const UPC = 'UPC';
    public const GTIN = 'GTIN';
    public const UPCA = 'UPCA';
    public const EAN13 = 'EAN13';
    public const UPCE0 = 'UPCE0';
    public const DISA = 'DISA';
    public const UAB = 'UAB';
    public const CODABAR = 'CODABAR';
    public const PRICE_EMBEDDED_UPC = 'PRICE_EMBEDDED_UPC';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::SKU, 'label' => __('SKU')],
            ['value' => self::UPC, 'label' => __('UPC')],
            ['value' => self::GTIN, 'label' => __('GTIN')],
            ['value' => self::UPCA, 'label' => __('UPCA')],
            ['value' => self::EAN13, 'label' => __('EAN13')],
            ['value' => self::UPCE0, 'label' => __('UPCE0')],
            ['value' => self::DISA, 'label' => __('DISA')],
            ['value' => self::UAB, 'label' => __('UAB')],
            ['value' => self::CODABAR, 'label' => __('CODABAR')],
            ['value' => self::PRICE_EMBEDDED_UPC, 'label' => __('Price embedded UPC')],
        ];
    }
}
