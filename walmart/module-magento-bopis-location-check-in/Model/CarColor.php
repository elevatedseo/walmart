<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\Model\AbstractModel;
use Walmart\BopisLocationCheckInApi\Api\Data\CarColorInterface;
use Walmart\BopisLocationCheckIn\Model\ResourceModel\CarColor as CarColorResourceModel;

class CarColor extends AbstractModel implements CarColorInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(CarColorResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getCarColorId(): ?int
    {
        $carColorId = $this->getData(self::CAR_COLOR_ID);

        return !empty($carColorId) ? (int)$carColorId : null;
    }

    /**
     * @inheritDoc
     */
    public function setCarColorId(int $carColorId): CarColorInterface
    {
        return $this->setData(self::CAR_COLOR_ID, $carColorId);
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
    public function setScope(?string $scope): CarColorInterface
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
    public function setScopeId(?int $scopeId): CarColorInterface
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
    public function setValue(string $value): CarColorInterface
    {
        return $this->setData(self::VALUE, $value);
    }
}
