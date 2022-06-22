<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Plugin\InventoryApi\Api;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\Framework\App\ResourceConnection;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateLocationsInterface;

/**
 * Class provides after Plugin on Magento\InventoryApi\Api\SourceRepositoryInterface::save
 * to remove sources from locations list if it was disabled during save process
 */
class SourceRepositoryInterfacePlugin
{
    /**
     * Cached resources singleton
     *
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Config             $config
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param null                      $result
     * @param SourceInterface           $source
     */
    public function afterSave(SourceRepositoryInterface $subject, $result, SourceInterface $source)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $connection = $this->resourceConnection->getConnection();
        $locationTableName = $this->resourceConnection->getTableName(
            AssociateLocationsInterface::ASSOCIATE_LOCATIONS_TABLE
        );

        if (!$source->isEnabled() || !$source->getExtensionAttributes()->getIsPickupLocationActive()) {
            $whereCondition = SourceInterface::SOURCE_CODE . " = " . "'" . $source->getSourceCode() . "'";
            $connection->delete($locationTableName, [$whereCondition]);
        }
    }
}
