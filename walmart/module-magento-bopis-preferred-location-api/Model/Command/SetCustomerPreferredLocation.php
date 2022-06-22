<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationApi\Model\Command;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;

/**
 * Set "selected_inventory_source" Customer attribute
 */
class SetCustomerPreferredLocation
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * Set Customer Preferred Location
     *
     * @param CustomerInterface $customer
     * @param string $sourceCode
     * @return bool
     */
    public function execute(CustomerInterface $customer, string $sourceCode): bool
    {
        $locationAttribute = $customer->getCustomAttribute(
            CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE
        );
        $locationCode = $locationAttribute ? $locationAttribute->getValue() : null;
        if ($locationCode == $sourceCode) {
            return true;
        }

        $customer->setCustomAttribute(
            CustomerCustomAttributesInterface::SELECTED_INVENTORY_SOURCE,
            $sourceCode
        );

        try {
            $this->customerRepository->save($customer);
            return true;
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Can\'t save Preferred Location "%s" for Customer with ID "%s": %s',
                    $sourceCode,
                    $customer->getId(),
                    $e->getMessage()
                )
            );
        }
        return false;
    }
}
