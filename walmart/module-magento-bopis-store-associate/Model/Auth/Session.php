<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\Auth;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;
use Walmart\BopisStoreAssociate\Model\Password\Validator as PasswordValidator;

/**
 * Represents the session data store associate user
 */
class Session
{
    /**
     * @var int
     */
    private const SESSION_TOKEN_LENGTH = 16;

    /**
     * @var AssociateUserInterface|null
     */
    private ?AssociateUserInterface $currentUser;

    /**
     * @var AssociateSessionInterface
     */
    private AssociateSessionInterface $currentSession;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @var Random
     */
    private Random $mathRandom;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var PasswordValidator
     */
    private PasswordValidator $passwordValidator;

    /**
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     * @param Random                              $mathRandom
     * @param TimezoneInterface                   $date
     * @param PasswordValidator                   $passwordValidator
     */
    public function __construct(
        AssociateSessionRepositoryInterface $associateSessionRepository,
        Random $mathRandom,
        TimezoneInterface $date,
        PasswordValidator $passwordValidator
    ) {
        $this->associateSessionRepository = $associateSessionRepository;
        $this->mathRandom = $mathRandom;
        $this->date = $date;
        $this->passwordValidator = $passwordValidator;
    }

    /**
     * Return current (successfully authenticated) user,
     *
     * @return AssociateUserInterface|null
     */
    public function getUser(): AssociateUserInterface
    {
        return $this->currentUser;
    }

    /**
     * @param AssociateUserInterface $currentUser
     */
    public function setUser(AssociateUserInterface $currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Return current (successfully authenticated) user session,
     *
     * @return AssociateSessionInterface|null
     */
    public function getCurrentSession(): AssociateSessionInterface
    {
        return $this->currentSession;
    }

    /**
     * @param AssociateSessionInterface $currentUser
     */
    public function setCurrentSession(AssociateSessionInterface $currentUser): void
    {
        $this->currentSession = $currentUser;
    }

    /**
     * Process of configuring of current auth when login was performed
     *
     * @return AssociateSessionInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function processLogin():AssociateSessionInterface
    {
        $currentSession = null;
        if ($user = $this->getUser()) {
            $existingSession = $this->getExistingSession($user);

            if ($existingSession->getSessionId()) {
                $currentSession = $this->updateSession($existingSession);
            } else {
                $currentSession = $this->createSession($user);
            }
        }

        $this->setCurrentSession($currentSession);

        return $currentSession;
    }

    /**
     * Generate random string for token
     *
     * @param int $length
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateToken(int $length): string
    {
        $token = $this->mathRandom->getRandomString($length, Random::CHARS_DIGITS . Random::CHARS_LOWERS);

        //theoretically existing token could be generated. We need to avoid it.
        $currentSession = $this->associateSessionRepository->getByToken($token);

        if ($currentSession->getSessionId()) {
            $this->generateToken(self::SESSION_TOKEN_LENGTH);
        }

        return $token;
    }

    /**
     * Update session for logged in user
     *
     * @param AssociateSessionInterface $existingSession
     *
     * @return AssociateSessionInterface
     * @throws CouldNotSaveException
     */
    private function updateSession(AssociateSessionInterface $existingSession): AssociateSessionInterface
    {
        $existingSession->setUpdatedAt($this->date->date()->format('Y-m-d H:i:s'));
        $this->associateSessionRepository->save($existingSession);

        return $existingSession;
    }

    /**
     * Create session for user
     *
     * @param AssociateUserInterface $user
     *
     * @return AssociateSessionInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function createSession(AssociateUserInterface $user): AssociateSessionInterface
    {
        $associateSession = $this->associateSessionRepository->create();

        $associateSession->setUserId($user->getUserId());
        if ($this->passwordValidator->checkExpiredPassword($user)) {
            $associateSession->setStatus(AssociateSessionInterface::STATUS_PASSWORD_EXPIRED);
        } else {
            $associateSession->setStatus(AssociateSessionInterface::STATUS_ACTIVE);
        }
        $associateSession->setToken($this->generateToken(self::SESSION_TOKEN_LENGTH));
        $associateSession->setCreatedAt($this->date->date()->format('Y-m-d H:i:s'));
        $associateSession->setUpdatedAt($this->date->date()->format('Y-m-d H:i:s'));

        $this->associateSessionRepository->save($associateSession);

        return $associateSession;
    }

    /**
     * Check if session for current user exists
     *
     * @param AssociateUserInterface $user
     *
     * @return AssociateSessionInterface
     */
    private function getExistingSession(AssociateUserInterface $user): AssociateSessionInterface
    {
        try {
            $currentSession = $this->associateSessionRepository->getByUserId($user->getUserId());
        } catch (NoSuchEntityException $e) {
            $currentSession = $this->associateSessionRepository->create();
        }

        return $currentSession;
    }
}
