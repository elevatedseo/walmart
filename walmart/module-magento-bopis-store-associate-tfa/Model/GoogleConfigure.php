<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Endroid\QrCode\Exception\GenerateImageException;
use Endroid\QrCode\Exception\ValidationException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;
use Walmart\BopisStoreAssociateApi\Model\SessionTokenHeader;
use Walmart\BopisStoreAssociateTfa\Model\Provider\Google;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleConfigureResponseInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\GoogleConfigureResponseInterfaceFactory;
use Walmart\BopisStoreAssociateTfaApi\Api\GoogleConfigureInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\StatusInterface as TfaStatusInterface;
use Walmart\BopisStoreAssociateTfaApi\Exception\ProviderNotAllowed;
use Walmart\BopisStoreAssociateTfaApi\Exception\TwoFactorNotAvailable;

/**
 * Represents the google provider
 */
class GoogleConfigure implements GoogleConfigureInterface
{
    /**
     * @var UserAuthenticator
     */
    private UserAuthenticator $userAuthenticator;

    /**
     * @var GoogleConfigureResponseInterfaceFactory
     */
    private GoogleConfigureResponseInterfaceFactory $googleConfigureResponseFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Google
     */
    private Google $google;

    /**
     * @param UserAuthenticator                       $userAuthenticator
     * @param GoogleConfigureResponseInterfaceFactory $googleConfigureResponseFactory
     * @param RequestInterface                        $request
     * @param Google                                  $google
     */
    public function __construct(
        UserAuthenticator $userAuthenticator,
        GoogleConfigureResponseInterfaceFactory $googleConfigureResponseFactory,
        RequestInterface $request,
        Google $google
    ) {
        $this->userAuthenticator = $userAuthenticator;
        $this->googleConfigureResponseFactory = $googleConfigureResponseFactory;
        $this->request = $request;
        $this->google = $google;
    }

    /**
     * Get the information required to configure google
     *
     * @return GoogleConfigureResponseInterface
     * @throws AuthorizationException
     * @throws CouldNotSaveException
     * @throws Exception
     * @throws GenerateImageException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws ValidationException
     * @throws ProviderNotAllowed
     * @throws TwoFactorNotAvailable
     */
    public function execute(): GoogleConfigureResponseInterface
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

        try {
            $user = $this->userAuthenticator->authenticateWithTokenAndProvider($sessionToken, Google::CODE);
        } catch (ProviderNotAllowed $exception) {
            throw new Exception(
                __($exception->getMessage()),
                0,
                Exception::HTTP_FORBIDDEN,
                [
                    'status' => TfaStatusInterface::TFA_PROVIDER_NOT_ALLOWED
                ]
            );
        } catch (TwoFactorNotAvailable $exception) {
            throw new Exception(
                __($exception->getMessage()),
                0,
                Exception::HTTP_FORBIDDEN,
                [
                    'status' => TfaStatusInterface::TFA_AUTHORIZATION_NOT_AVAILABLE
                ]
            );
        } catch (AuthorizationException $exception) {
            throw new Exception(
                __($exception->getMessage()),
                0,
                Exception::HTTP_FORBIDDEN,
                [
                    'status' => StatusInterface::SESSION_TOKEN_INVALID
                ]
            );
        }

        return $this->googleConfigureResponseFactory->create(
            [
                'data' => [
                    GoogleConfigureResponseInterface::QR_BASE64_IMAGE => base64_encode($this->google->getQrCodeAsPng($user)),
                    GoogleConfigureResponseInterface::SECRET_CODE => $this->google->getSecretCode($user)
                ]
            ]
        );
    }
}
