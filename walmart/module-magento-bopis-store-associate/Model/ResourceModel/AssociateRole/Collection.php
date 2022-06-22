<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisStoreAssociate\Model\AssociateRole;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole as AssociateRoleResourceModel;

/**
 * Resource Collection of Associate Role entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = AssociateRoleResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AssociateRole::class, AssociateRoleResourceModel::class);
    }
}
