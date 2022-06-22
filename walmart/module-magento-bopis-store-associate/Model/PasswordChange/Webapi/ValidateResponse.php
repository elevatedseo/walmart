<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\PasswordChange\Webapi;

use Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterface;

/**
 * Prepare final data for password change request
 */
class ValidateResponse
{
    /**
     * Prepare final response data depending on password change validation
     *
     * @param PasswordChangeResponseInterface $dataObject
     * @param array                           $result
     *
     * @return array
     */
    public function execute(PasswordChangeResponseInterface $dataObject, array $result): array
    {
        if ($dataObject->getSuccess()) {
            unset($result[PasswordChangeResponseInterface::STATUS]);
            unset($result[PasswordChangeResponseInterface::MESSAGE]);
        }

        return $result;
    }
}
