<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model\ResourceModel\CheckIn;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisLocationCheckIn\Model\CheckIn;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CheckIn as CheckInResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = CheckInResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CheckIn::class, CheckInResourceModel::class);
    }
}
