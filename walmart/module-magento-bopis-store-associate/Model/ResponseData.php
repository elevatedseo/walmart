<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateLocationsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociate\Model\Auth\Session;
use Walmart\BopisStoreAssociate\Model\Password\Validator as PasswordValidator;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterfaceFactory;

/**
 * ResponseData is responsible for populating data for login response
 */
class ResponseData
{
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var LoginResponseInterfaceFactory
     */
    private LoginResponseInterfaceFactory $loginResponseFactory;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var PasswordValidator
     */
    private PasswordValidator $passwordValidator;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @var AssociateRoleRepositoryInterface
     */
    private AssociateRoleRepositoryInterface $associateRoleRepository;

    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @var ParametersResponseInterfaceFactory
     */
    private ParametersResponseInterfaceFactory $parametersResponseFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param LoginResponseInterfaceFactory $loginResponseFactory
     * @param \Walmart\Session $session
     * @param PasswordValidator $passwordValidator
     * @param TimezoneInterface $date
     * @param ConfigProvider $configProvider
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     * @param Json $jsonSerializer
     * @param ParametersResponseInterfaceFactory $parametersResponseFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        LoginResponseInterfaceFactory $loginResponseFactory,
        Session $session,
        PasswordValidator $passwordValidator,
        TimezoneInterface $date,
        ConfigProvider $configProvider,
        AssociateRoleRepositoryInterface $associateRoleRepository,
        Json $jsonSerializer,
        ParametersResponseInterfaceFactory $parametersResponseFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->loginResponseFactory = $loginResponseFactory;
        $this->session = $session;
        $this->passwordValidator = $passwordValidator;
        $this->date = $date;
        $this->configProvider = $configProvider;
        $this->associateRoleRepository = $associateRoleRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->parametersResponseFactory = $parametersResponseFactory;
    }

    /**
     * Populate response data for login request
     *
     * @param AssociateUserInterface $user
     *
     * @return LoginResponseInterface
     */
    public function getResponseData(AssociateUserInterface $user): LoginResponseInterface
    {
        $response = $this->loginResponseFactory->create();

        /* @var AssociateSessionInterface $session */
        $currentSession = $this->session->getCurrentSession();

        $responseData = [
            LoginResponseInterface::SESSION_TOKEN => $currentSession->getToken(),
            LoginResponseInterface::SESSION_LIFETIME => $this->configProvider->getSessionLifetime(),
        ];

        $parameters = $this->parametersResponseFactory->create();
        $parametersData = [
            ParametersResponseInterface::SUCCESS => true,
            ParametersResponseInterface::PASSWORD_EXPIRES => $this->getConvertedExpiredPassword($user),
            ParametersResponseInterface::PASSWORD_CHANGE_REQUIRED => $this->getPasswordChangeRequired($user, $currentSession)
        ];

        $this->dataObjectHelper->populateWithArray($parameters, $parametersData, ParametersResponseInterface::class);
        $responseData[LoginResponseInterface::PARAMETERS] = $parameters;

        if (!$this->getPasswordChangeRequired($user, $currentSession)) {
            $responseData[LoginResponseInterface::FIRSTNAME] = $user->getFirstname();
            $responseData[LoginResponseInterface::LASTNAME] = $user->getLastname();
            $responseData[LoginResponseInterface::PERMISSIONS] = $this->getPermissionsData($user->getRoleId());
            $responseData[LoginResponseInterface::ALL_LOCATIONS] = $user->getAllLocations();
            $responseData[LoginResponseInterface::LOCATIONS] = $this->prepareLocationsData($user->getLocations());
        }

        $this->dataObjectHelper->populateWithArray($response, $responseData, LoginResponseInterface::class);

        return $response;
    }

    /**
     *
     * @param AssociateUserInterface $user
     * @param AssociateSessionInterface $currentSession
     *
     * @return bool
     */
    private function getPasswordChangeRequired(AssociateUserInterface $user, AssociateSessionInterface $currentSession): bool
    {
        $isLatestPasswordExpired = $this->isLatestPasswordExpired($currentSession);

        return $isLatestPasswordExpired || $user->getPasswordGenerated();
    }

    /**
     * Check whether the latest password is expired
     *
     * @param AssociateSessionInterface $currentSession
     *
     * @return bool|void
     */
    private function isLatestPasswordExpired(AssociateSessionInterface $currentSession): bool
    {
        return $currentSession->getStatus() === AssociateSessionInterface::STATUS_PASSWORD_EXPIRED;
    }

    /**
     * Covert date to RFC2822 format
     *
     * @param AssociateUserInterface $user
     *
     * @return string
     */
    private function getConvertedExpiredPassword(AssociateUserInterface $user): string
    {
        try {
            $currentPassword = $this->passwordValidator->getCurrentPassword($user);
            $date = $this->date->date(
                (int)strtotime($currentPassword->getUpdatedAt()) + $this->passwordValidator->getAdminPasswordLifetime()
            );

            return $date->format(\DateTimeInterface::RFC2822);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get rid of odd data of locations entity such as user_id, name
     *
     * @param array $locations
     *
     * @return array
     */
    private function prepareLocationsData(array $locations): array
    {
        $processedLocations = [];
        foreach ($locations as $location) {
            $processedLocations[] = ['code' => $location[AssociateLocationsInterface::SOURCE_CODE]];
        }

        return $processedLocations;
    }

    /**
     * Get permissions for provided role id
     *
     * @param int|null $roleId
     *
     * @return array
     */
    private function getPermissionsData(?int $roleId): array
    {
        $permissions = [];

        if ($roleId) {
            try {
                $role = $this->associateRoleRepository->get($roleId);

                if ($role->getAllPermissions()) {
                    $permissions = array_keys(AssociateRoleInterface::PERMISSIONS_LIST_DATA);
                } else {
                    $permissions = $role->getPermissionList() ?
                        $this->jsonSerializer->unserialize($role->getPermissionList()) : [];
                }

            } catch (NoSuchEntityException $e) {
                return [];
            }
        }

        return $permissions;
    }
}
