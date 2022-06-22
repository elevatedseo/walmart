<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Order\Grid\Collection;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes as OrderExtensionAttributesResourceModel;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

/**
 * Class provides before Plugin on Magento\Sales\Model\ResourceModel\Order\Grid\Collection::load
 * to add bopis_queue_status extension attribute to order grid collection
 */
class AddSyncStatusColumnPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Collection $subject
     * @return null
     * @throws LocalizedException
     */
    public function beforeLoad(Collection $subject)
    {
        if (!$this->config->isEnabled()) {
            return null;
        }

        if (!$subject->isLoaded()) {
            $primaryKey = $subject->getResource()->getIdFieldName();
            $tableName = $subject->getResource()->getTable(OrderExtensionAttributesResourceModel::TABLE_NAME);

            $subject->getSelect()->joinLeft(
                $tableName,
                $tableName . '.' .  OrderExtensionAttributesInterface::ORDER_ID . ' = main_table.' . $primaryKey,
                $tableName . '.' . OrderExtensionAttributesInterface::BOPIS_QUEUE_STATUS
            );
        }

        return null;
    }
}
