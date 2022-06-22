<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociate\Model\ResponseData;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;
use Walmart\BopisStoreAssociateApi\Model\SessionTokenHeader;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateTfa\Model\Provider\Google;
use Walmart\BopisStoreAssociateTfaApi\Api\GoogleAuthenticateInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleTfaExtendedLoginResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\StatusInterface as TfaStatusInterface;

/**
 * Authenticate with google provider
 */
class GoogleAuthenticate implements GoogleAuthenticateInterface
{
    /**
     * @var UserAuthenticator
     */
    private UserAuthenticator $userAuthenticator;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Google
     */
    private Google $google;

    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @var ResponseData
     */
    private ResponseData $responseData;

    /**
     * @var AssociateTfaConfigRepositoryInterface
     */
    private AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository;

    /**
     * @param UserAuthenticator                     $userAuthenticator
     * @param RequestInterface                      $request
     * @param Google                                $google
     * @param DataObjectFactory                     $dataObjectFactory
     * @param ResponseData                          $responseData
     * @param AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
     */
    public function __construct(
        UserAuthenticator $userAuthenticator,
        RequestInterface $request,
        Google $google,
        DataObjectFactory $dataObjectFactory,
        ResponseData $responseData,
        AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
    ) {
        $this->userAuthenticator = $userAuthenticator;
        $this->request = $request;
        $this->google = $google;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->responseData = $responseData;
        $this->associateTfaConfigRepository = $associateTfaConfigRepository;
    }

    /**
     * @param string $otp
     *
     * @return LoginResponseInterface
     * @throws AuthorizationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute($otp): LoginResponseInterface
    {
        $sessionToken = $this->request->getHeader(SessionTokenHeader::NAME);

        if (!$sessionToken) {
            throw new Exception(
                __('Session token is not provided.'),
                0,
                Exception::HTTP_BAD_REQUEST,
                [
                    StatusInterface::SESSION_TOKEN_MISSING
                ]
            );
        }

        $user = $this->userAuthenticator->authenticateWithTokenAndProvider($sessionToken, Google::CODE);

        if (!$this->isProviderActiveForUser($user->getUserId(), Google::CODE)) {
            throw new Exception(
                __('Provider is not allowed.'),
                0,
                Exception::HTTP_UNAUTHORIZED,
                [
                    'status' => TfaStatusInterface::TFA_PROVIDER_NOT_ALLOWED
                ]
            );
        }

        $data = $this->dataObjectFactory->create(
            [
                'data' => [
                    'tfa_code' => $otp
                ]
            ]
        );

        if ($this->google->verify($user, $data)) {
            $this->userAuthenticator->grantAccess();
            $responseData = $this->responseData->getResponseData($user);
            $responseData->setData(GoogleTfaExtendedLoginResponseInterface::TFA_SUCCESS, true);

            return $responseData;
        }

        throw new Exception(
            __('Invalid code.'),
            0,
            Exception::HTTP_UNAUTHORIZED,
            [
                'status' => TfaStatusInterface::INVALID_OTP_CODE
            ]
        );
    }

    /**
     * @param int    $userId
     * @param string $providedCode
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isProviderActiveForUser($userId, $providedCode)
    {
        $userTfaConfig = $this->associateTfaConfigRepository->getByUserId($userId);

        return $userTfaConfig->getProvider() == $providedCode;
    }
}
