<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

/**
 * Interface For Car Make Model
 *
 * @api
 */
interface CarMakeInterface
{
    public const CAR_MAKE_ID = 'carmake_id';
    public const SCOPE = 'scope';
    public const SCOPE_ID = 'scope_id';
    public const VALUE = 'value';

    /**
     * @return int|null
     */
    public function getCarMakeId(): ?int;

    /**
     * @param int $carMakeId
     *
     * @return $this
     */
    public function setCarMakeId(int $carMakeId): self;

    /**
     * @return string|null
     */
    public function getScope(): ?string;

    /**
     * @param string|null $scope
     *
     * @return $this
     */
    public function setScope(?string $scope): self;

    /**
     * @return int|null
     */
    public function getScopeId(): ?int;

    /**
     * @param int|null $scopeId
     *
     * @return $this
     */
    public function setScopeId(?int $scopeId): self;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $value): self;
}
