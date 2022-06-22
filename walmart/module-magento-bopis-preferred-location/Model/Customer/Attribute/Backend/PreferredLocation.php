<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model\Customer\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;

/**
 * Customer attribute backend model for Stock Source code validation
 */
class PreferredLocation extends AbstractBackend
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * Set NULL if Stock Source doesn't exist
     *
     * @param DataObject $object
     * @return void
     */
    public function beforeSave($object)
    {
        $selectedInventorySource = $object->getData(CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE);
        if (empty($selectedInventorySource) || !$this->isStockSourceExists($selectedInventorySource)) {
            $object->setData(CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE, null);
        }
    }

    /**
     * Check is Stock Source exists
     *
     * @param string $sourceCode
     * @return bool
     */
    private function isStockSourceExists(string $sourceCode): bool
    {
        try {
            $this->sourceRepository->get($sourceCode);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
