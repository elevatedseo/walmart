<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api;

use Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface;

/**
 * @api
 */
interface ConfirmationStatusInterface
{
    public const DESCRIPTION = 'description';
    public const REASON = 'reason';

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     *
     * @return void
     */
    public function setDescription(string $description): void;

    /**
     * @return \Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface|null
     */
    public function getReason(): ?ReasonInterface;

    /**
     * @param \Walmart\BopisOrderFaasSync\Api\Confirmation\ReasonInterface|null $reason
     *
     * @return void
     */
    public function setReason(?ReasonInterface $reason): void;
}
