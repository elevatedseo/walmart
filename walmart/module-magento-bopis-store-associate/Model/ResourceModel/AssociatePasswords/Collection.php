<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisStoreAssociate\Model\AssociatePasswords;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociatePasswords as AssociatePasswordsResourceModel;

/**
 * Resource Collection of Associate Passwords entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = AssociatePasswordsResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AssociatePasswords::class, AssociatePasswordsResourceModel::class);
    }
}
