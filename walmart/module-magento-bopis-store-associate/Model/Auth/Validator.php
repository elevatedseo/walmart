<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\Auth;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociate\Model\ConfigProvider;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;

/**
 * User log in validation
 */
class Validator
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @param EncryptorInterface               $encryptor
     * @param Session                          $session
     * @param TimezoneInterface                $date
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param ConfigProvider                   $configProvider
     */
    public function __construct(
        EncryptorInterface $encryptor,
        Session $session,
        TimezoneInterface $date,
        AssociateUserRepositoryInterface $associateUserRepository,
        ConfigProvider $configProvider
    ) {
        $this->encryptor = $encryptor;
        $this->session = $session;
        $this->date = $date;
        $this->associateUserRepository = $associateUserRepository;
        $this->configProvider = $configProvider;
    }

    /**
     * Validate user credentials
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     * @throws AuthenticationException
     */
    public function validateCredentials(string $username, string $password): void
    {
        if (!is_string($username) || strlen($username) == 0) {
            throw new AuthenticationException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'username'])
            );
        }
        if (!is_string($password) || strlen($password) == 0) {
            throw new AuthenticationException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'password'])
            );
        }
    }

    /**
     * Ensure that provided password matches the current user password. Check if the current user account is active.
     *
     * @param AssociateUserInterface $user
     * @param string                 $password
     *
     * @return bool
     * @throws AuthenticationException
     * @throws CouldNotSaveException
     */
    public function verifyIdentity(AssociateUserInterface $user, string $password): bool
    {
        $this->checkFailuresNum($user);
        $this->validatePassword($user, $password);
        $this->isUserActive($user);

        return true;
    }

    /**
     * Check if entered password is match user password
     *
     * @param AssociateUserInterface $user
     * @param string $password
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function validatePassword(AssociateUserInterface $user, string $password): void
    {
        if (!$this->encryptor->validateHash($password, $user->getPassword())) {
            $this->logFailAttempt($user);
            throw new Exception(
                __('There is no user matching provided combination of username and password.'),
                0,
                Exception::HTTP_UNAUTHORIZED,
                [
                    'status' => StatusInterface::INCORRECT_USERNAME_PASSWORD
                ]
            );
        }
    }

    /**
     * Check if user is active
     *
     * @param AssociateUserInterface $user
     *
     * @return void
     * @throws Exception
     */
    private function isUserActive(AssociateUserInterface $user): void
    {
        if ($user->getIsActive()) {
            $now = strtotime($this->date->date()->format('Y-m-d H:i:s'));
            $userActiveFrom = $user->getActiveFrom() ? strtotime($user->getActiveFrom()) : null;
            $userActiveTo = $user->getActiveTo() ? strtotime($user->getActiveTo()) : null;

            if (($userActiveFrom === null || $userActiveFrom <= $now)
                && ($userActiveTo === null || $userActiveTo >= $now)
            ) {
                return;
            }
        }

        throw new Exception(
            __('Account no longer active. Please contact support.'),
            0,
            Exception::HTTP_UNAUTHORIZED,
            [
                'status' => StatusInterface::USER_ACCOUNT_NOT_ACTIVE
            ]
        );
    }

    /**
     * check if account is locked due to failures number
     *
     * @param AssociateUserInterface $user
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function checkFailuresNum(AssociateUserInterface $user): void
    {
        $now = strtotime($this->date->date()->format('Y-m-d H:i:s'));
        $lockoutExpires = $user->getLockExpires() ? strtotime($user->getLockExpires()) : null;

        if ($lockoutExpires) {
            if ($lockoutExpires <= $now) {
                $this->clearFailuresNum($user);//unlock account if it was locked and time has passed
            } else {
                throw new Exception(
                    $this->getErrorMessage(),
                    0,
                    Exception::HTTP_UNAUTHORIZED,
                    [
                        'status' => StatusInterface::USER_ACCOUNT_LOCKED
                    ]
                );
            }
        }
    }

    /**
     * log fail attempt
     *
     * @param AssociateUserInterface $user
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function logFailAttempt(AssociateUserInterface $user): void
    {
        $failuresNum = $user->getFailuresNum();
        $failuresNum++;
        $user->setFailuresNum($failuresNum);

        if ($this->configProvider->getLockoutFailures() > 0
            && $user->getFailuresNum() >= $this->configProvider->getLockoutFailures()) {
                $lockoutExpires = $this->date->date()
                    ->modify('+'  . $this->configProvider->getLockoutThreshold() * 60 . ' seconds')
                    ->format('Y-m-d H:i:s');
                $user->setLockExpires($lockoutExpires);
        }

        $this->associateUserRepository->save($user);
    }

    /**
     * clear failures number after successful login
     *
     * @param AssociateUserInterface $user
     *
     * @throws CouldNotSaveException
     */
    public function clearFailuresNum(AssociateUserInterface $user): void
    {
        $user->setFailuresNum(0);
        $user->setLockExpires(null);
        $this->associateUserRepository->save($user);
    }

    /**
     * @return Phrase
     */
    private function getErrorMessage(): Phrase
    {
        $threshold = $this->configProvider->getLockoutThreshold();
        return __(
            "This user account is currently locked. Please try again in {$threshold} minutes or contact your administrator."
        );
    }
}
