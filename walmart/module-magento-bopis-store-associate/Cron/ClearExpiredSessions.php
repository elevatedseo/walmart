<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Cron;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\ConfigProvider;

/**
 * Check if session is expired and invalidate it
 */
class ClearExpiredSessions
{
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
     * @var Config
     */
    private Config $config;

    /**
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     * @param ConfigProvider                      $configProvider
     * @param TimezoneInterface                   $date
     * @param Config                              $config
     */
    public function __construct(
        AssociateSessionRepositoryInterface $associateSessionRepository,
        ConfigProvider $configProvider,
        TimezoneInterface $date,
        Config $config
    ) {
        $this->associateSessionRepository = $associateSessionRepository;
        $this->configProvider = $configProvider;
        $this->date = $date;
        $this->config = $config;
    }

    /**
     * Invalidate expired sessions
     *
     * @throws CouldNotDeleteException
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $sessions = $this->associateSessionRepository->getList();

        foreach ($sessions->getItems() as $session) {
            $now = strtotime($this->date->date()->format('Y-m-d H:i:s'));
            $expiredAt = (int)strtotime($session->getUpdatedAt()) + $this->configProvider->getSessionLifetime();

            if ($expiredAt <= $now) {
                $this->associateSessionRepository->delete($session);
            }
        }
    }
}
