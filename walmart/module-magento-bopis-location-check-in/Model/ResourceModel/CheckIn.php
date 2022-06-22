<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

class CheckIn extends AbstractDb
{
    public const ID_FIELD_NAME = CheckInInterface::CHECKIN_ID;
    public const TABLE_NAME = 'walmart_bopis_sales_order_checkin';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
