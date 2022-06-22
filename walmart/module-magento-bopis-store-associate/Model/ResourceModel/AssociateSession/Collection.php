<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisStoreAssociate\Model\AssociateSession;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateSession as AssociateSessionResourceModel;

/**
 * Resource Collection of Associate Session entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = AssociateSessionResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AssociateSession::class, AssociateSessionResourceModel::class);
    }
}
