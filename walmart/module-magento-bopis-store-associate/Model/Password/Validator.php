<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\Password;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociatePasswordsInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Walmart\BopisStoreAssociate\Model\ConfigProvider;

/**
 * User log in validation
 */
class Validator
{
    /**
     * Maximum length of admin password
     */
    private const MAX_PASSWORD_LENGTH = 256;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var AssociatePasswordsRepositoryInterface
     */
    private AssociatePasswordsRepositoryInterface $associatePasswordsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var StringHelper
     */
    private StringHelper $stringHelper;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @param EncryptorInterface                    $encryptor
     * @param TimezoneInterface                     $date
     * @param AssociatePasswordsRepositoryInterface $associatePasswordsRepository
     * @param SearchCriteriaBuilder                 $searchCriteriaBuilder
     * @param StringHelper                          $stringHelper
     * @param SortOrderBuilder                      $sortOrderBuilder
     * @param ConfigProvider                        $configProvider
     */
    public function __construct(
        EncryptorInterface $encryptor,
        TimezoneInterface $date,
        AssociatePasswordsRepositoryInterface $associatePasswordsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StringHelper $stringHelper,
        SortOrderBuilder $sortOrderBuilder,
        ConfigProvider $configProvider
    ) {
        $this->encryptor = $encryptor;
        $this->date = $date;
        $this->associatePasswordsRepository = $associatePasswordsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stringHelper = $stringHelper;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->configProvider = $configProvider;
    }

    /**
     * Check if latest password is expired
     *
     * @param  AssociateUserInterface $user
     * @return bool
     */
    public function isLatestPasswordExpired(AssociateUserInterface $user): bool
    {
        try {
            $currentUserPassword = $this->getCurrentPassword($user);
            if (!$currentUserPassword->getUpdatedAt() || $this->getAdminPasswordLifetime() == 0) {
                return false;
            }
            $now = strtotime($this->date->date()->format('Y-m-d H:i:s'));
            return (int)strtotime($currentUserPassword->getUpdatedAt()) + $this->getAdminPasswordLifetime() < $now;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getAdminPasswordLifetime(): int
    {
        return 86400 * $this->configProvider->getPasswordLifetime();
    }

    /**
     * Side-effect can be when passwords were changed with different lifetime configuration settings
     *
     * @param AssociateUserInterface $user
     *
     * @return bool
     */
    public function checkExpiredPassword(AssociateUserInterface $user): bool
    {
        if ($this->configProvider->getPasswordIsForced()) {
            return $this->isLatestPasswordExpired($user);
        } else {
            return false;
        }
    }

    /**
     * @param  AssociateUserInterface $user
     * @return AssociatePasswordsInterface
     * @throws \Exception
     */
    public function getCurrentPassword(AssociateUserInterface $user): AssociatePasswordsInterface
    {
        $this->searchCriteriaBuilder
            ->addFilter(AssociatePasswordsInterface::IS_ACTIVE, 1)
            ->addFilter(AssociatePasswordsInterface::USER_ID, $user->getUserId());
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $passwordList = $this->associatePasswordsRepository->getList($searchCriteria);

        if ($passwordList->getTotalCount()) {
            $passwordList = $passwordList->getItems();
            return array_shift($passwordList);
        }

        throw new NoSuchEntityException(__('Associate password not found'));
    }

    /**
     * Make sure that password complies with minimum security requirements.
     *
     * @param string $password
     *
     * @return void
     * @throws InputException
     */
    private function checkPasswordStrength(string $password): void
    {
        $length = $this->stringHelper->strlen($password);
        if ($length > self::MAX_PASSWORD_LENGTH) {
            throw new InputException(
                __(
                    'Please enter a password with at most %1 characters.',
                    self::MAX_PASSWORD_LENGTH
                )
            );
        }
        $configMinPasswordLength = $this->configProvider->getMinimumPasswordLength();
        if ($length < $configMinPasswordLength) {
            throw new InputException(
                __(
                    'The password needs at least %1 characters. Create a new password and try again.',
                    $configMinPasswordLength
                )
            );
        }
        if ($this->stringHelper->strlen(trim($password)) != $length) {
            throw new InputException(
                __("The password can't begin or end with a space. Verify the password and try again.")
            );
        }

        $requiredCharactersCheck = $this->makeRequiredCharactersCheck($password);
        if ($requiredCharactersCheck !== 0) {
            throw new InputException(
                __(
                    'Minimum of different classes of characters in password is %1.' .
                    ' Classes of characters: Lower Case, Upper Case, Digits, Special Characters.',
                    $requiredCharactersCheck
                )
            );
        }
    }

    /**
     * Check password for presence of required character sets
     *
     * @param string $password
     *
     * @return int
     */
    private function makeRequiredCharactersCheck(string $password): int
    {
        $counter = 0;
        $requiredNumber = $this->configProvider->getRequiredCharacterClassNumber();
        $return = 0;

        if (preg_match('/[0-9]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[A-Z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[a-z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[^a-zA-Z0-9]+/', $password)) {
            $counter++;
        }

        if ($counter < $requiredNumber) {
            $return = $requiredNumber;
        }

        return $return;
    }

    /**
     * @param AssociateUserInterface $user
     * @param string                 $password
     *
     * @return void
     * @throws InputException
     */
    public function validateNewPassword(AssociateUserInterface $user, string $password): void
    {
        $this->checkPasswordStrength($password);
        $this->checkOldPasswords($user, $password);
    }

    /**
     * Validate Inputs
     *
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return void
     * @throws InputException
     */
    public function validateInputs(string $oldPassword, string $newPassword): void
    {
        if (!is_string($oldPassword) || strlen($oldPassword) == 0) {
            throw new InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'old_password'])
            );
        }
        if (!is_string($newPassword) || strlen($newPassword) == 0) {
            throw new InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'new_password'])
            );
        }
    }

    /**
     * Check if entered password is match user password
     *
     * @param AssociateUserInterface $user
     * @param string                 $password
     *
     * @throws AuthenticationException
     */
    public function verifyOldPassword(AssociateUserInterface $user, string $password): void
    {
        if (!$this->encryptor->validateHash($password, $user->getPassword())) {
            throw new AuthenticationException(
                __('Old password does not match our records. Please try again.')
            );
        }
    }

    /**
     * New password is compared to at least 5 previous passwords to prevent setting them again
     *
     * @param AssociateUserInterface $user
     * @param string                 $password
     *
     * @return void
     * @throws InputException
     */
    private function checkOldPasswords(AssociateUserInterface $user, string $password): void
    {
        $this->searchCriteriaBuilder
            ->addFilter(AssociatePasswordsInterface::USER_ID, $user->getUserId());

        $sortOrder = $this->sortOrderBuilder->setField(AssociatePasswordsInterface::UPDATED_AT)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);

        $passwordList = $this->associatePasswordsRepository->getList($searchCriteria);

        if ($passwordList->getTotalCount()) {
            // Check whether password was used before
            foreach ($passwordList->getItems() as $oldPasswordHash) {
                if ($this->encryptor->isValidHash($password, $oldPasswordHash->getPassword())) {
                    throw new InputException(
                        __('Sorry, but this password has already been used. Please create another.')
                    );
                }
            }
        }
    }
}
