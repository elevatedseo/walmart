<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Exception;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociate\Model\Auth\Session;
use Walmart\BopisStoreAssociateTfaApi\Exception\ProviderNotAllowed;
use Walmart\BopisStoreAssociateTfaApi\Exception\TwoFactorNotAvailable;

/**
 * Retrieves users from credentials and enforced throttling
 */
class UserAuthenticator
{
    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @param AssociateUserRepositoryInterface    $associateUserRepository
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     * @param Session                             $session
     * @param ConfigProvider                      $configProvider
     */
    public function __construct(
        AssociateUserRepositoryInterface $associateUserRepository,
        AssociateSessionRepositoryInterface $associateSessionRepository,
        Session $session,
        ConfigProvider $configProvider
    ) {
        $this->associateUserRepository = $associateUserRepository;
        $this->associateSessionRepository = $associateSessionRepository;
        $this->session = $session;
        $this->configProvider = $configProvider;
    }

    /**
     * Obtain a user with an id and a tfa token
     *
     * @param string $sessionToken
     * @param string $providerCode
     *
     * @return AssociateUserInterface
     * @throws AuthorizationException
     * @throws LocalizedException
     * @throws ProviderNotAllowed
     * @throws TwoFactorNotAvailable
     */
    public function authenticateWithTokenAndProvider(string $sessionToken, string $providerCode): AssociateUserInterface
    {
        if (!in_array($providerCode, $this->configProvider->getProviders(), true)) {
            throw new ProviderNotAllowed((string)__('Provider is not allowed.'));
        }

        if (!$this->configProvider->getIsTfaEnabled()) {
            throw new TwoFactorNotAvailable((string)__('Two-factor authorization is not available.'));
        }

        try {
            $session = $this->associateSessionRepository->getByToken($sessionToken);
            if (!$session->getSessionId()) {
                throw new LocalizedException(__('Invalid session token'));
            }
            $user = $this->associateUserRepository->get($session->getUserId());
            $this->session->setCurrentSession($session);
        } catch (Exception $e) {
            throw new AuthorizationException(
                __('Invalid session token')
            );
        }

        return $user;
    }

    /**
     * Set 2FA session as passed
     *
     * @throws CouldNotSaveException
     */
    public function grantAccess(): void
    {
        $session = $this->session->getCurrentSession();
        $session->setStatus(AssociateSessionInterface::STATUS_TFA_PASSED);
        $this->associateSessionRepository->save($session);
    }
}
