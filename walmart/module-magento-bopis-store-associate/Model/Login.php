<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\LoginInterface;

/**
 * @inheritdoc
 */
class Login implements LoginInterface
{
    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * @var ResponseData
     */
    private ResponseData $responseData;

    /**
     * @param Auth         $auth
     * @param ResponseData $responseData
     */
    public function __construct(
        Auth $auth,
        ResponseData $responseData
    ) {
        $this->auth = $auth;
        $this->responseData = $responseData;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return LoginResponseInterface
     * @throws AuthenticationException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(string $username, string $password): LoginResponseInterface
    {
        $user = $this->auth->performLogin($username, $password);

        return $this->responseData->getResponseData($user);
    }
}
