<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Plugin\BopisStoreAssociate\Model\Login\Webapi;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociate\Model\Login\Webapi\ValidateResponse;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociate\Model\Auth\Session;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateTfa\Model\ConfigProvider;
use Walmart\BopisStoreAssociateTfa\Model\Config\Source\TfaPolicyOptions;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleTfaExtendedLoginResponseInterface;

/**
 * Class provides after Plugin on Walmart\BopisStoreAssociate\Model\Login\Webapi\ValidateResponse::execute
 * to extend login response depending on TFA configurations
 */
class ValidateResponsePlugin
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var AssociateTfaConfigRepositoryInterface
     */
    private AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Session                               $session
     * @param AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
     * @param ConfigProvider                        $configProvider
     * @param Config                                $config
     */
    public function __construct(
        Session $session,
        AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository,
        ConfigProvider $configProvider,
        Config $config
    ) {
        $this->session = $session;
        $this->associateTfaConfigRepository = $associateTfaConfigRepository;
        $this->configProvider = $configProvider;
        $this->config = $config;
    }

    /**
     * Change login response depending on TFA configurations
     *
     * @param ValidateResponse       $subject
     * @param array                  $processedResult
     * @param LoginResponseInterface $dataObject
     * @param array                  $result
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterExecute(
        ValidateResponse $subject,
        array $processedResult,
        LoginResponseInterface $dataObject,
        array $result
    ): array {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $sortedResult[LoginResponseInterface::SESSION_TOKEN] =
            $processedResult[LoginResponseInterface::SESSION_TOKEN];
        $sortedResult[LoginResponseInterface::SESSION_LIFETIME] =
            $processedResult[LoginResponseInterface::SESSION_LIFETIME];
        $sortedResult[LoginResponseInterface::PARAMETERS] =
            $processedResult[LoginResponseInterface::PARAMETERS];
        $sortedResult[GoogleTfaExtendedLoginResponseInterface::TFA_AVAILABLE] =
            $this->getTfaAvailable();

        if ($this->getTfaAvailable()) {
            $sortedResult[GoogleTfaExtendedLoginResponseInterface::TFA_PROVIDERS] =
                $this->getTfaProviders();
            $sortedResult[LoginResponseInterface::PARAMETERS][GoogleTfaExtendedLoginResponseInterface::TFA_CONFIGURED] =
                $this->getTfaConfigured();
            $sortedResult[LoginResponseInterface::PARAMETERS][GoogleTfaExtendedLoginResponseInterface::TFA_CONFIGURATION_REQUIRED] =
                $this->getTfaConfigurationRequired();
            if ($this->showTfaSuccess()) {
                $sortedResult[GoogleTfaExtendedLoginResponseInterface::TFA_SUCCESS] =
                    $this->getTfaSuccess($dataObject) || $this->getTfaPassed();
            }
        }

        if (!$this->getTfaAvailable() || $this->validateTfa()) {
            if (isset($processedResult[LoginResponseInterface::FIRSTNAME])) {
                $sortedResult[LoginResponseInterface::FIRSTNAME] =
                    $processedResult[LoginResponseInterface::FIRSTNAME];
            }
            if (isset($processedResult[LoginResponseInterface::LASTNAME])) {
                $sortedResult[LoginResponseInterface::LASTNAME] =
                    $processedResult[LoginResponseInterface::LASTNAME];
            }
            if (isset($processedResult[LoginResponseInterface::PERMISSIONS])) {
                $sortedResult[LoginResponseInterface::PERMISSIONS] =
                    $processedResult[LoginResponseInterface::PERMISSIONS];
            }
            if (isset($processedResult[LoginResponseInterface::ALL_LOCATIONS])) {
                $sortedResult[LoginResponseInterface::ALL_LOCATIONS] =
                    $processedResult[LoginResponseInterface::ALL_LOCATIONS];
            }
            if (isset($processedResult[LoginResponseInterface::LOCATIONS])) {
                $sortedResult[LoginResponseInterface::LOCATIONS] =
                    $processedResult[LoginResponseInterface::LOCATIONS];
            }
        }

        return $sortedResult;
    }

    /**
     * @return bool
     */
    private function getTfaAvailable(): bool
    {
        return $this->configProvider->getIsTfaEnabled();
    }

    /**
     * @return array
     */
    private function getTfaProviders(): array
    {
        return $this->configProvider->getProviders();
    }

    /**
     * @return bool
     */
    private function getTfaConfigured(): bool
    {
        $session = $this->session->getCurrentSession();
        try {
            $tfaConfig = $this->associateTfaConfigRepository->getByUserId($session->getUserId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return (bool)$tfaConfig->getConfigId();
    }

    /**
     * @return bool
     */
    private function getTfaConfigurationRequired(): bool
    {
         return $this->configProvider->getTfaPolicy() === TfaPolicyOptions::MANDATORY_OPTION_CODE;
    }

    /**
     * @param  LoginResponseInterface $dataObject
     * @return bool
     */
    private function getTfaSuccess(LoginResponseInterface $dataObject): bool
    {
        return (bool)$dataObject->getData(GoogleTfaExtendedLoginResponseInterface::TFA_SUCCESS);
    }

    /**
     * @return bool
     */
    private function getTfaPassed(): bool
    {
        return $this->session->getCurrentSession()->getStatus() === AssociateSessionInterface::STATUS_TFA_PASSED;
    }

    /**
     * @return bool
     */
    private function getIfTfaMandatory(): bool
    {
        return $this->configProvider->getTfaPolicy() === TfaPolicyOptions::MANDATORY_OPTION_CODE;
    }

    /**
     * validate TFA success depending on TFA policy configuration
     *
     * @return bool
     */
    private function validateTfa(): bool
    {
        if ($this->getIfTfaMandatory()) {
            return $this->getTfaPassed();
        } else { //If Tfa Policy is optional
            if ($this->getTfaConfigured()) {
                return $this->getTfaPassed();
            } else {
                return true;
            }
        }
    }

    /**
     * If show TFA success field in response
     *
     * @return bool
     */
    private function showTfaSuccess(): bool
    {
        if ($this->getIfTfaMandatory()) {
            return true;
        } else { //If Tfa Policy is optional
            if ($this->getTfaConfigured()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
