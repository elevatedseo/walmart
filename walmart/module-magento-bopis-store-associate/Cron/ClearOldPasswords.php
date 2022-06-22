<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;

/**
 * Review user's previous passwords and remove them if there are more than 5 rows. We store only 5 previous passwords.
 */
class ClearOldPasswords
{
    /**
     * @var AssociatePasswordsRepositoryInterface
     */
    private AssociatePasswordsRepositoryInterface $associatePasswordsRepository;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param AssociatePasswordsRepositoryInterface $associatePasswordsRepository
     * @param TimezoneInterface                     $date
     * @param SearchCriteriaBuilder                 $searchCriteriaBuilder
     * @param SortOrderBuilder                      $sortOrderBuilder
     * @param Config                                $config
     */
    public function __construct(
        AssociatePasswordsRepositoryInterface $associatePasswordsRepository,
        TimezoneInterface $date,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Config $config
    ) {
        $this->associatePasswordsRepository = $associatePasswordsRepository;
        $this->date = $date;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $groupedPasswordsByUser = [];

        $this->searchCriteriaBuilder
            ->addFilter(AssociatePasswordsInterface::IS_ACTIVE, 0);

        $sortOrder = $this->sortOrderBuilder->setField(AssociatePasswordsInterface::UPDATED_AT)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);

        $passwordList = $this->associatePasswordsRepository->getList($searchCriteria);

        foreach ($passwordList->getItems() as $password) {
            $groupedPasswordsByUser[$password->getUserId()][] = $password;
        }

        foreach ($groupedPasswordsByUser as $passwords) {
            $passwordsCount = count($passwords);

            if ($passwordsCount > AssociatePasswordsInterface::NUMBER_OF_PASSWORDS_TO_STORE) {
                for ($i = AssociatePasswordsInterface::NUMBER_OF_PASSWORDS_TO_STORE; $i < $passwordsCount; $i++) {
                    $this->associatePasswordsRepository->delete($passwords[$i]);
                }
            }
        }
    }
}
