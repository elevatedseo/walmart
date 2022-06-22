<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisLocationCheckInApi\Api\Data\CarMakeInterface;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarMake as CarMakeResourceModel;

class CarMake extends AbstractModel implements CarMakeInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(CarMakeResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getCarMakeId(): ?int
    {
        $carMakeId = $this->getData(self::CAR_MAKE_ID);

        return !empty($carMakeId) ? (int)$carMakeId : null;
    }

    /**
     * @inheritDoc
     */
    public function setCarMakeId(int $carMakeId): CarMakeInterface
    {
        return $this->setData(self::CAR_MAKE_ID, $carMakeId);
    }

    /**
     * @inheritDoc
     */
    public function getScope(): ?string
    {
        $scope = $this->getData(self::SCOPE);

        return !empty($scope) ? (string)$scope : null;
    }

    /**
     * @inheritDoc
     */
    public function setScope(?string $scope): CarMakeInterface
    {
        return $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritDoc
     */
    public function getScopeId(): ?int
    {
        $scopeId = $this->getData(self::SCOPE_ID);

        return !empty($scopeId) ? (int)$scopeId : null;
    }

    /**
     * @inheritDoc
     */
    public function setScopeId(?int $scopeId): CarMakeInterface
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return (string)$this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $value): CarMakeInterface
    {
        return $this->setData(self::VALUE, $value);
    }
}
