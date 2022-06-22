<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;

/**
 * Associate role resource model
 */
class AssociateRole extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = AssociateRoleInterface::ROLE_ID;
    const TABLE_NAME = 'walmart_bopis_associate_role';

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
