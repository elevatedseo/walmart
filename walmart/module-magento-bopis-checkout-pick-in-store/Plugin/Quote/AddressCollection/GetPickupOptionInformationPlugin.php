<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Quote\AddressCollection;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionInterface;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ResourceModel\Quote\Address\Collection;

/**
 * Load Pickup Option and add to Extension Attributes.
 */
class GetPickupOptionInformationPlugin
{
    private const TABLE_ALIAS = 'bipoqa';

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private AddressExtensionInterfaceFactory $addressExtensionInterfaceFactory;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $connection;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param AddressExtensionInterfaceFactory $addressExtensionInterfaceFactory
     * @param ResourceConnection               $connection
     * @param Config                           $config
     */
    public function __construct(
        AddressExtensionInterfaceFactory $addressExtensionInterfaceFactory,
        ResourceConnection $connection,
        Config $config
    ) {
        $this->addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
        $this->connection = $connection;
        $this->config = $config;
    }

    /**
     * Load information about Pickup Option to collection of Quote Address.
     *
     * @param Collection $collection
     * @param \Closure $proceed
     * @param bool $printQuery
     * @param bool $logQuery
     *
     * @return Collection
     * @throws \Zend_Db_Select_Exception
     */
    public function aroundLoadWithFilter(
        Collection $collection,
        \Closure $proceed,
        bool $printQuery,
        bool $logQuery
    ): Collection {
        if (!$this->config->isEnabled() || $collection->isLoaded()) {
            return $proceed($printQuery, $logQuery);
        }

        if (!isset($collection->getSelect()->getPart(Select::FROM)[self::TABLE_ALIAS])) {
            $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_quote_address', 'checkout');
            $collection->getSelect()->joinLeft(
                [self::TABLE_ALIAS => $table],
                self::TABLE_ALIAS . '.address_id = main_table.address_id',
                [PickupOptionInterface::PICKUP_OPTION]
            );
        }

        $result = $proceed($printQuery, $logQuery);

        foreach ($collection as $address) {
            $this->processAddress($address);
        }

        return $result;
    }

    /**
     * Process address entity.
     *
     * @param Address $address
     *
     * @return void
     */
    private function processAddress(Address $address): void
    {
        $hasDataChanges = $address->hasDataChanges();
        if ($address->getData(PickupOptionInterface::PICKUP_OPTION)) {
            $this->addPickupOptionToExtensionAttributes($address);
        }
        $address->unsetData(PickupOptionInterface::PICKUP_OPTION);
        $address->setDataChanges($hasDataChanges);
    }

    /**
     * Add Loaded Pickup Option to Extension Attributes.
     *
     * @param Address $item
     *
     * @return void
     */
    private function addPickupOptionToExtensionAttributes(Address $item): void
    {
        if (!$item->getExtensionAttributes()) {
            $item->setExtensionAttributes($this->addressExtensionInterfaceFactory->create());
        }

        $item->getExtensionAttributes()->setPickupOption(
            $item->getData(PickupOptionInterface::PICKUP_OPTION)
        );
    }
}
