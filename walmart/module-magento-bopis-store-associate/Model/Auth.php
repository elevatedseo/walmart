<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\Auth\Validator;
use Walmart\BopisStoreAssociate\Model\Auth\Session;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;

/**
 * Store associate user auth model
 */
class Auth
{
    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param TimezoneInterface                $date
     * @param Session                          $session
     * @param Validator                        $validator
     */
    public function __construct(
        AssociateUserRepositoryInterface $associateUserRepository,
        TimezoneInterface $date,
        Session $session,
        Validator $validator
    ) {
        $this->associateUserRepository = $associateUserRepository;
        $this->date = $date;
        $this->session = $session;
        $this->validator = $validator;
    }

    /**
     * Perform login process
     *
     * @param string $username
     * @param string $password
     *
     * @return AssociateUserInterface|null
     * @throws AuthenticationException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function performLogin(string $username, string $password): AssociateUserInterface
    {
        $this->validator->validateCredentials($username, $password);
        $this->login($username, $password);

        if ($this->session->getUser()) {
            $this->session->processLogin();
            $this->validator->clearFailuresNum($this->session->getUser());
        } else {
            throw new Exception(
                __('The account sign-in was incorrect or your account is disabled temporarily'),
                0,
                Exception::HTTP_UNAUTHORIZED,
                [
                    'status' => StatusInterface::USER_ACCOUNT_LOCKED
                ]
            );
        }

        return $this->session->getUser();
    }

    /**
     * Authenticate user name and password and save loaded record
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     * @throws LocalizedException
     */
    public function authenticate(string $username, string $password): bool
    {
        $result = false;

        try {
            $associateUser = $this->associateUserRepository->getByUsername($username);

            if ($associateUser->getUserId()) {
                $result = $this->validator->verifyIdentity($associateUser, $password);

                if ($result) {
                    $this->session->setUser($associateUser);
                }
            }
        } catch (NoSuchEntityException $e) {
            throw new Exception(
                __('There is no user matching provided combination of username and password.'),
                0,
                Exception::HTTP_UNAUTHORIZED,
                [
                    'status' => StatusInterface::INCORRECT_USERNAME_PASSWORD
                ]
            );
        }

        return $result;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return void
     * @throws LocalizedException
     */
    public function login(string $username, string $password): void
    {
        if ($this->authenticate($username, $password)) {
            $this->recordLogin();
        }
    }

    /**
     * log last successful login
     *
     * @return AssociateUserInterface|null
     * @throws CouldNotSaveException
     */
    private function recordLogin(): AssociateUserInterface
    {
        $user = $this->session->getUser();
        $user->setLastLoggedAt($this->date->date()->format('Y-m-d H:i:s'));
        $this->associateUserRepository->save($user);

        return $user;
    }
}
