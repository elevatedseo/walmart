<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;

/**
 * Associate session resource model
 */
class AssociateSession extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = AssociateSessionInterface::SESSION_ID;
    const TABLE_NAME = 'walmart_bopis_associate_session';

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
