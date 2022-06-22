<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface;

class CarMake extends AbstractDb
{
    public const ID_FIELD_NAME = CarMakeInterface::CAR_MAKE_ID;
    public const TABLE_NAME = 'walmart_bopis_carmake';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
