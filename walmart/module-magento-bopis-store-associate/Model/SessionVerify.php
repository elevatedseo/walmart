<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\SessionVerifyResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\SessionVerifyResponseInterfaceFactory;
use Walmart\BopisStoreAssociateApi\Api\SessionVerifyInterface;
use Walmart\BopisStoreAssociateApi\Api\StatusInterface;
use Walmart\BopisStoreAssociateApi\Model\SessionTokenHeader;

/**
 * @inheritdoc
 */
class SessionVerify implements SessionVerifyInterface
{
    /**
     * @var SessionVerifyResponseInterfaceFactory
     */
    private SessionVerifyResponseInterfaceFactory $sessionVerifyResponseFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @param SessionVerifyResponseInterfaceFactory $sessionVerifyResponseFactory
     * @param RequestInterface $request
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     */
    public function __construct(
        SessionVerifyResponseInterfaceFactory $sessionVerifyResponseFactory,
        RequestInterface $request,
        AssociateSessionRepositoryInterface $associateSessionRepository
    ) {
        $this->sessionVerifyResponseFactory = $sessionVerifyResponseFactory;
        $this->request = $request;
        $this->associateSessionRepository = $associateSessionRepository;
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws LocalizedException
     */
    public function execute(): SessionVerifyResponseInterface
    {
        $sessionToken = $this->request->getHeader(SessionTokenHeader::NAME);

        if (!$sessionToken) {
            throw new LocalizedException(__('Session token is not provided.'));
        }

        $session = $this->associateSessionRepository->getByToken($sessionToken);
        if (!$session->getSessionId()) {
            throw new Exception(
                __('Session was not found or expired.'),
                null,
                Exception::HTTP_FORBIDDEN,
                [
                    'status' => StatusInterface::SESSION_NOT_FOUND_INVALID
                ]
            );
        }

        return $this->sessionVerifyResponseFactory->create(
            [
                'data' => [
                    SessionVerifyResponseInterface::RESULT => true
                ]
            ]
        );
    }
}
