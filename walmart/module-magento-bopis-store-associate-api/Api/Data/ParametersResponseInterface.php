<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

/**
 * @api
 */
interface ParametersResponseInterface
{
    public const SUCCESS = 'success';
    public const PASSWORD_EXPIRES = 'password_expires';
    public const PASSWORD_CHANGE_REQUIRED = 'password_change_required';

    /**
     * Get success
     *
     * @return bool
     */
    public function getSuccess(): ?bool;

    /**
     * Set success
     *
     * @param  bool $success
     * @return void
     */
    public function setSuccess(bool $success): void;

    /**
     * Get password expires
     *
     * @return string|null
     */
    public function getPasswordExpires(): ?string;

    /**
     * Set password expires
     *
     * @param string|null $passwordExpires
     *
     * @return void
     */
    public function setPasswordExpires(?string $passwordExpires): void;

    /**
     * Get password change required
     *
     * @return bool|null
     */
    public function getPasswordChangeRequired(): ?bool;

    /**
     * Set password change required
     *
     * @param  bool|null $passwordChangeRequired
     * @return void
     */
    public function setPasswordChangeRequired(?bool $passwordChangeRequired): void;
}
