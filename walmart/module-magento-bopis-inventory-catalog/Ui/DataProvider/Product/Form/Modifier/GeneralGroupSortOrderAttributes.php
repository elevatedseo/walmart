<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class GeneralGroupSortOrderAttributes extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private ArrayManager $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @param  array $meta
     * @return array|mixed
     */
    public function modifyMeta(array $meta)
    {
        return $this->modifySortOrder($meta);
    }

    /**
     * @param  $meta
     * @return mixed
     */
    protected function modifySortOrder($meta)
    {
        $this->arrayManager->replace(
            'product-details/children/container_status/arguments/data/config/sortOrder',
            $meta,
            1
        );
        $this->arrayManager->replace(
            'product-details/children/container_available_home_delivery/arguments/data/config/sortOrder',
            $meta,
            2
        );
        $this->arrayManager->replace(
            'product-details/children/container_available_store_pickup/arguments/data/config/sortOrder',
            $meta,
            3
        );
        $this->arrayManager->replace(
            'product-details/children/container_name/arguments/data/config/sortOrder',
            $meta,
            50
        );

        return $meta;
    }
}
