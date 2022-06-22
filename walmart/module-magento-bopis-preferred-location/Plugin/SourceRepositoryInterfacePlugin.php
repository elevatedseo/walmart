<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;

/**
 * Set NULL for all Preferred Locations if source becomes disabled
 */
class SourceRepositoryInterfacePlugin
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param CustomerRepositoryInterface  $customerRepository
     * @param Config                       $config
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        CustomerRepositoryInterface $customerRepository,
        Config $config
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->customerRepository = $customerRepository;
        $this->config = $config;
    }

    /**
     * Check is status has changed and reset Preferred Location for affected Customers
     *
     * @param SourceRepositoryInterface $subject
     * @param null $result
     * @param SourceInterface $source
     * @return null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function afterSave(SourceRepositoryInterface $subject, $result, SourceInterface $source)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!$source->isEnabled() && $source->dataHasChangedFor('enabled')) {
            $searchCriteria = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria->addFilter(
                CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE,
                $source->getSourceCode()
            );
            $customersResult = $this->customerRepository->getList($searchCriteria->create());
            foreach ($customersResult->getItems() as $customer) {
                $customer->setCustomAttribute(
                    CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE,
                    null
                );
                $this->customerRepository->save($customer);
            }
        }

        return $result;
    }
}
