<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api\Confirmation;

interface ReasonInterface
{
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CREATED = 'CREATED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const CODE = 'code';
    public const DESCRIPTION = 'description';

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string|null $code
     *
     * @return void
     */
    public function setCode(?string $code): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description): void;
}
