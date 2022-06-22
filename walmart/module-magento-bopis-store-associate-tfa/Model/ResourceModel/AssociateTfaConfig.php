<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface;

/**
 * Associate tfa config resource model
 */
class AssociateTfaConfig extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = AssociateTfaConfigInterface::CONFIG_ID;
    const TABLE_NAME = 'walmart_bopis_associate_tfa_config';

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
