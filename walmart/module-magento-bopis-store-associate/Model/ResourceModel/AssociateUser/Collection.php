<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateLocationsInterface;
use Walmart\BopisStoreAssociate\Model\AssociateUser;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateUser as AssociateUserResourceModel;

/**
 * Resource Collection of Associate User entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = AssociateUserResourceModel::ID_FIELD_NAME;

    /**
     * @var array
     */
    private array $linkLocations = [];

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(AssociateUser::class, AssociateUserResourceModel::class);
    }

    /**
     * @return void|Collection
     */
    protected function _beforeLoad()
    {
        $this->loadLinkedLocations();
    }

    /**
     * Add locations to collection items
     *
     * @param  DataObject $item
     * @return DataObject
     */
    protected function beforeAddLoadedItem(DataObject $item): DataObject
    {
        parent::beforeAddLoadedItem($item);
        $associatedLocations = $this->linkLocations[$item->getUserId()] ?? [];
        $item->setLocations($associatedLocations);

        return $item;
    }

    /**
     * @return array
     */
    private function loadLinkedLocations(): array
    {
        $sortedLocations = [];
        if (!$this->linkLocations) {
            $select = $this->getConnection()->select()->from(
                $this->getConnection()->getTableName(AssociateLocationsInterface::ASSOCIATE_LOCATIONS_TABLE)
            );

            $allLocations = $this->getConnection()->fetchAll($select);

            foreach ($allLocations as $location) {
                $sortedLocations[$location[AssociateLocationsInterface::USER_ID]][] =
                    $location[AssociateLocationsInterface::SOURCE_CODE];
            }

            $this->linkLocations = $sortedLocations;
        }

        return $this->linkLocations;
    }
}
