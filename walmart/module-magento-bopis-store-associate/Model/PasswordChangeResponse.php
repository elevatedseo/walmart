<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\DataObject;
use Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\PasswordChangeResponseInterface;

/**
 * @inheritdoc
 */
class PasswordChangeResponse extends DataObject implements PasswordChangeResponseInterface
{
    /**
     * @return \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface
     */
    public function getParameters(): ParametersResponseInterface
    {
        return $this->getData(self::PARAMETERS);
    }

    /**
     * @param \Walmart\BopisStoreAssociateApi\Api\Data\ParametersResponseInterface $parameters
     *
     * @return void
     */
    public function setParameters(ParametersResponseInterface $parameters): void
    {
        $this->setData(self::PARAMETERS, $parameters);
    }
}
