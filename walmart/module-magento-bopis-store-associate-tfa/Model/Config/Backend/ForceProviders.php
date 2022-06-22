<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Walmart\BopisStoreAssociateTfa\Model\Config\Source\Providers;

/**
 * Managing "Force Providers" config value.
 */
class ForceProviders extends Value
{
    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (empty($value)) {
            $this->setValue(Providers::PROVIDER_GOOGLE_CODE);
        }

        return parent::beforeSave();
    }
}
