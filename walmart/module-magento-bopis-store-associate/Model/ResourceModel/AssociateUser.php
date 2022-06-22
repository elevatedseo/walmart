<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\ResourceModel;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\InventoryApi\Api\Data\SourceInterface as InventorySourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateLocationsInterface;

/**
 * Associate user resource model
 */
class AssociateUser extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = AssociateUserInterface::USER_ID;
    const TABLE_NAME = 'walmart_bopis_associate_user';

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param Context                   $context
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param null                      $connectionName
     */
    public function __construct(
        Context $context,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }

    /**
     * @param  AbstractModel $associateUser
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $associateUser): AssociateUser
    {
        $connection = $this->getConnection();

        try {
            $connection->beginTransaction();
            parent::save($associateUser);
            if ($associateUser->dataHasChangedFor('locations')) {
                $this->updateLocations($associateUser->getUserId(), $associateUser->getLocations());
            }
            $connection->commit();

            return $this;
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update locations on saving associate user
     *
     * @param int   $userId
     * @param array $currentLocations
     */
    protected function updateLocations(int $userId, $currentLocations = []): void
    {
        $locationTableName = $this->getTable(AssociateLocationsInterface::ASSOCIATE_LOCATIONS_TABLE);
        $connection = $this->getConnection();

        $locationsToDelete = [];
        $newLocations = [];

        foreach ($this->getLinkedLocations($userId) as $oldLinkedLocation) {
            $locationsToDelete[] = $oldLinkedLocation[AssociateLocationsInterface::SOURCE_CODE];
        }

        if ($currentLocations) {
            foreach ($currentLocations as $location) {
                $data = [
                    AssociateLocationsInterface::USER_ID => $userId,
                    AssociateLocationsInterface::SOURCE_CODE => $location ?? '',
                ];

                $key = array_search($location, $locationsToDelete);
                if (is_int($key)) {
                    unset($locationsToDelete[$key]);
                } else {
                    $newLocations[] = $data;
                }
            }
        }

        if ($newLocations) {
            $connection->insertMultiple($locationTableName, $newLocations);
        }

        if ($locationsToDelete) {
            $locationsString = '';
            foreach ($locationsToDelete as $location) {
                $locationsString.= "'" . $location . "',";
            }
            $locationsString = substr($locationsString, 0, -1);

            $whereCondition = AssociateLocationsInterface::USER_ID . ' = ' . $userId . ' AND '
                . AssociateLocationsInterface::SOURCE_CODE . ' IN (' . $locationsString . ')';
            $connection->delete($locationTableName, [$whereCondition]);
        }
    }

    /**
     * @param  int $userId
     * @return array
     */
    protected function getLinkedLocations(int $userId): array
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(AssociateLocationsInterface::ASSOCIATE_LOCATIONS_TABLE)
        )->where(
            AssociateLocationsInterface::USER_ID . ' = ?',
            $userId
        );

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return array
     */
    private function getAllLocationsCollection(): array
    {
        $locations = [];

        $this->searchCriteriaBuilder
            ->addFilter(InventorySourceInterface::ENABLED, 1)
            ->addFilter(PickupLocationInterface::IS_PICKUP_LOCATION_ACTIVE, 1);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $inventorySources = $this->sourceRepository->getList($searchCriteria);

        foreach ($inventorySources->getItems() as $source) {
            $locations[] = ['source_code' => $source->getSourceCode(), 'name' => $source->getName()];
        }

        return $locations;
    }

    /**
     * Add locations to user model
     *
     * @param  AbstractModel $associateUser
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $associateUser): AbstractDb
    {
        if ($associateUser->getAllLocations()) {
            $associateUser->setLocations(
                $this->getAllLocationsCollection()
            );
        } else {
            $associateUser->setLocations(
                $this->getLinkedLocations($associateUser->getUserId())
            );
        }
        return parent::_afterLoad($associateUser);
    }
}
