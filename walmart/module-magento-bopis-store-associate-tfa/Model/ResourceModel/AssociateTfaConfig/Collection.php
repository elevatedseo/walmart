<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisStoreAssociateTfa\Model\AssociateTfaConfig;
use Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig as AssociateAssociateTfaConfigResourceModel;

/**
 * Resource Collection of Associate Tfa Config entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = AssociateAssociateTfaConfigResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AssociateTfaConfig::class, AssociateAssociateTfaConfigResourceModel::class);
    }
}
