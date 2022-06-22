<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model\ResourceModel\CarMake;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisLocationCheckIn\Model\CarMake;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarMake as CarMakeResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = CarMakeResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CarMake::class, CarMakeResourceModel::class);
    }
}
