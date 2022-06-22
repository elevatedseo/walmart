<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface;

/**
 * Associate Tfa Config Manager
 */
class UserConfigManager
{
    /**
     * @var array|null
     */
    private ?array $tfaConfigurationRegistry = [];

    /**
     * @var AssociateTfaConfigRepositoryInterface
     */
    private AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository;

    /**
     * @param AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
     */
    public function __construct(
        AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
    ) {
        $this->associateTfaConfigRepository = $associateTfaConfigRepository;
    }

    /**
     * @param  int         $userId
     * @param  string      $providerCode
     * @param  string|null $secret
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function setProviderConfig(int $userId, string $providerCode, string $secret = null): bool
    {
        $userConfig = $this->getUserConfiguration($userId);

        if (!$userConfig->getProvider()) {
            $userConfig->setProvider($providerCode);
        }

        $config = [
            'user_id' => $userId,
            'provider' => $providerCode,
            'secret' => $secret,
        ];
        $userConfig->setEncodedConfig($config);
        $userConfig->setUserId($userId);
        $this->associateTfaConfigRepository->save($userConfig);

        return true;
    }

    /**
     * Get user TFA config
     *
     * @param  int $userId
     * @return AssociateTfaConfigInterface
     * @throws NoSuchEntityException
     */
    public function getUserConfiguration(int $userId): AssociateTfaConfigInterface
    {
        if (!isset($this->tfaConfigurationRegistry[$userId])) {
            $userConfig = $this->associateTfaConfigRepository->getByUserId($userId);
            if (!$userConfig->getUserId()) {
                $this->associateTfaConfigRepository->create();
            }

            $this->tfaConfigurationRegistry[$userId] = $userConfig;
        }

        return $this->tfaConfigurationRegistry[$userId];
    }
}
