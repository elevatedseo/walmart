<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInApi\Api\Data;

/**
 * Interface For Car Color Model
 *
 * @api
 */
interface CarColorInterface
{
    public const CAR_COLOR_ID = 'carcolor_id';
    public const SCOPE = 'scope';
    public const SCOPE_ID = 'scope_id';
    public const VALUE = 'value';

    /**
     * @return int|null
     */
    public function getCarColorId(): ?int;

    /**
     * @param int $carColorId
     *
     * @return $this
     */
    public function setCarColorId(int $carColorId): self;

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
