<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Plugin\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\AssociateUserRepository;
use Walmart\BopisStoreAssociate\Model\Password\Validator;

/**
 * Class provides after Plugin on Walmart\BopisStoreAssociate\Model\AssociateUserRepository::save
 * to save new password entity to passwords table
 */
class AssociateUserRepositoryPlugin
{
    /**
     * @var AssociatePasswordsRepositoryInterface
     */
    private AssociatePasswordsRepositoryInterface $associatePasswordsRepository;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @var Validator
     */
    private Validator $passwordValidator;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param AssociatePasswordsRepositoryInterface $associatePasswordsRepository
     * @param TimezoneInterface                     $date
     * @param Validator                             $passwordValidator
     * @param Config                                $config
     */
    public function __construct(
        AssociatePasswordsRepositoryInterface $associatePasswordsRepository,
        TimezoneInterface $date,
        Validator $passwordValidator,
        Config $config
    ) {
        $this->associatePasswordsRepository = $associatePasswordsRepository;
        $this->date = $date;
        $this->passwordValidator = $passwordValidator;
        $this->config = $config;
    }

    /**
     * @param AssociateUserRepository $subject
     * @param AssociateUserInterface  $result
     *
     * @return AssociateUserInterface
     * @throws CouldNotSaveException
     */
    public function afterSave(AssociateUserRepository $subject, AssociateUserInterface $result): AssociateUserInterface
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($result->dataHasChangedFor(AssociateUserInterface::PASSWORD)) {
            $this->deactivateOldPassword($result);
            $associatePassword = $this->associatePasswordsRepository->create();
            $associatePassword->setUserId($result->getUserId());
            $associatePassword->setIsActive(true);
            $associatePassword->setPassword($result->getPassword());
            $associatePassword->setUpdatedAt($this->date->date()->format('Y-m-d H:i:s'));

            $this->associatePasswordsRepository->save($associatePassword);
        }

        return $result;
    }

    /**
     * @param AssociateUserInterface $user
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function deactivateOldPassword(AssociateUserInterface $user): void
    {
        try {
            $currentPassword = $this->passwordValidator->getCurrentPassword($user);
            $currentPassword->setIsActive(false);
            $this->associatePasswordsRepository->save($currentPassword);
        } catch (NoSuchEntityException $e) {
            //do nothing. There is no old password
        }
    }
}
