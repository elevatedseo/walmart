<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\PasswordChangeInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterfaceFactory;
use Walmart\BopisStoreAssociate\Model\Password\Validator;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;
use Walmart\BopisStoreAssociateApi\Model\SessionTokenHeader;

/**
 * @inheritdoc
 */
class PasswordChange implements PasswordChangeInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var PasswordChangeResponseInterfaceFactory
     */
    private PasswordChangeResponseInterfaceFactory $passwordChangeResponseFactory;

    /**
     * @var Validator
     */
    private Validator $passwordValidator;

    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @var ParametersResponseInterfaceFactory
     */
    private ParametersResponseInterfaceFactory $parametersResponseFactory;

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @param PasswordChangeResponseInterfaceFactory $passwordChangeResponseFactory
     * @param RequestInterface                       $request
     * @param Validator                              $passwordValidator
     * @param AssociateUserRepositoryInterface       $associateUserRepository
     * @param AssociateSessionRepositoryInterface    $associateSessionRepository
     * @param ParametersResponseInterfaceFactory     $parametersResponseFactory
     * @param DataObjectHelper                       $dataObjectHelper
     */
    public function __construct(
        PasswordChangeResponseInterfaceFactory $passwordChangeResponseFactory,
        RequestInterface $request,
        Validator $passwordValidator,
        AssociateUserRepositoryInterface $associateUserRepository,
        AssociateSessionRepositoryInterface $associateSessionRepository,
        ParametersResponseInterfaceFactory $parametersResponseFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->request = $request;
        $this->passwordChangeResponseFactory = $passwordChangeResponseFactory;
        $this->passwordValidator = $passwordValidator;
        $this->associateUserRepository = $associateUserRepository;
        $this->associateSessionRepository = $associateSessionRepository;
        $this->parametersResponseFactory = $parametersResponseFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return PasswordChangeResponseInterface
     * @throws LocalizedException
     */
    public function execute(string $oldPassword, string $newPassword): PasswordChangeResponseInterface
    {
        $sessionToken = $this->request->getHeader(SessionTokenHeader::NAME);

        if (!$sessionToken) {
            $this->throwException(
                __('Session token is not provided.'),
                Exception::HTTP_BAD_REQUEST,
                [
                    'status' => StatusInterface::SESSION_TOKEN_MISSING
                ]
            );
        }
        $this->passwordValidator->validateInputs($oldPassword, $newPassword);

        $session = $this->associateSessionRepository->getByToken($sessionToken);
        if (!$session->getSessionId()) {
            $this->throwException(
                __('Invalid session token'),
                Exception::HTTP_BAD_REQUEST,
                [
                    'status' => StatusInterface::SESSION_TOKEN_INVALID
                ]
            );
        }
        $user = $this->associateUserRepository->get($session->getUserId());

        try {
            $this->passwordValidator->verifyOldPassword($user, $oldPassword);
            $this->passwordValidator->validateNewPassword($user, $newPassword);
            $user->setPassword($newPassword);
            $user->setPasswordGenerated(false);
            $this->associateUserRepository->save($user);
            $this->associateSessionRepository->deleteByUserId($session->getUserId());
        } catch (AuthenticationException $e) {
            $this->throwException(
                __($e->getMessage()),
                Exception::HTTP_FORBIDDEN,
                [
                    'success' => false,
                    'status' => StatusInterface::WRONG_PASSWORD
                ]
            );
        } catch (InputException $e) {
            $this->throwException(
                __("New password doesn't match requirements. %1", $e->getMessage()),
                Exception::HTTP_FORBIDDEN,
                [
                    'success' => false,
                    'status' => StatusInterface::NEW_PASSWORD_INSUFFICIENT
                ]
            );
        }

        $parameters = $this->parametersResponseFactory->create();
        $parametersData = [
            ParametersResponseInterface::SUCCESS => true,
            ParametersResponseInterface::PASSWORD_EXPIRES => null,
            ParametersResponseInterface::PASSWORD_CHANGE_REQUIRED => null,
        ];
        $this->dataObjectHelper->populateWithArray($parameters, $parametersData, ParametersResponseInterface::class);

        $response = $this->passwordChangeResponseFactory->create();
        $responseData = [
            PasswordChangeResponseInterface::PARAMETERS => $parameters
        ];
        $this->dataObjectHelper->populateWithArray($response, $responseData, PasswordChangeResponseInterface::class);

        return $response;
    }

    /**
     * @param Phrase $message
     * @param int $httpCode
     * @param array $parameters
     *
     * @return mixed
     * @throws Exception
     */
    public function throwException(Phrase $message, int $httpCode, array $parameters = []): void
    {
        throw new Exception(
            $message,
            0,
            $httpCode,
            $parameters
        );
    }
}
