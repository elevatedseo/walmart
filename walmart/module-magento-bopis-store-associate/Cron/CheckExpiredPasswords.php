<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;
use Walmart\BopisStoreAssociate\Model\ConfigProvider;

/**
 * Invalidate session if password has expired
 */
class CheckExpiredPasswords
{
    /**
     * @var AssociatePasswordsRepositoryInterface
     */
    private AssociatePasswordsRepositoryInterface $associatePasswordsRepository;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param AssociatePasswordsRepositoryInterface $associatePasswordsRepository
     * @param AssociateSessionRepositoryInterface   $associateSessionRepository
     * @param ConfigProvider                        $configProvider
     * @param TimezoneInterface                     $date
     * @param SearchCriteriaBuilder                 $searchCriteriaBuilder
     * @param Config                                $config
     */
    public function __construct(
        AssociatePasswordsRepositoryInterface $associatePasswordsRepository,
        AssociateSessionRepositoryInterface $associateSessionRepository,
        ConfigProvider $configProvider,
        TimezoneInterface $date,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->associatePasswordsRepository = $associatePasswordsRepository;
        $this->associateSessionRepository = $associateSessionRepository;
        $this->configProvider = $configProvider;
        $this->date = $date;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        if ($this->configProvider->getPasswordIsForced()) {
            $this->searchCriteriaBuilder
                ->addFilter(AssociatePasswordsInterface::IS_ACTIVE, 1);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $passwordList = $this->associatePasswordsRepository->getList($searchCriteria);

            foreach ($passwordList->getItems() as $password) {
                $now = strtotime($this->date->date()->format('Y-m-d H:i:s'));
                $expiredAt = (int)strtotime($password->getUpdatedAt()) + $this->getAdminPasswordLifetime();

                if ($expiredAt <= $now) {
                    $this->invalidateSessionForExpiredPassword($password);
                }
            }
        }
    }

    /**
     * @return int
     */
    private function getAdminPasswordLifetime(): int
    {
        return 86400 * $this->configProvider->getPasswordLifetime();
    }

    /**
     * @param  AssociatePasswordsInterface $password
     * @throws CouldNotDeleteException
     */
    private function invalidateSessionForExpiredPassword(AssociatePasswordsInterface $password): void
    {
        try {
            $session = $this->associateSessionRepository->getByUserId($password->getUserId());
            $this->associateSessionRepository->delete($session);
        } catch (NoSuchEntityException $e) {
            //Session doesn't exist for the user, do nothing
        } catch (CouldNotDeleteException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
