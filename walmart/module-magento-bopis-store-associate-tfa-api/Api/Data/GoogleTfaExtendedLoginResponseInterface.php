<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api\Data;

use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;

/**
 * Represents Google authenticate response data
 *
 * @api
 */
interface GoogleTfaExtendedLoginResponseInterface extends LoginResponseInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const TFA_AVAILABLE = 'tfa_available';
    public const TFA_PROVIDERS = 'tfa_providers';
    public const TFA_CONFIGURED = 'tfa_configured';
    public const TFA_CONFIGURATION_REQUIRED = 'tfa_configuration_required';
    public const TFA_SUCCESS = 'tfa_success';

    /**
     * Get tfa available
     *
     * @return bool
     */
    public function getTfaAvailable(): ?bool;

    /**
     * Set tfa available
     *
     * @param  bool $tfaAvailable
     * @return void
     */
    public function setTfaAvailable(bool $tfaAvailable): void;

    /**
     * Get tfa providers
     *
     * @return string[]
     */
    public function getTfaProviders(): ?array;

    /**
     * Set tfa providers
     *
     * @param  array $tfaProviders
     * @return void
     */
    public function setTfaProviders(array $tfaProviders): void;

    /**
     * Get tfa configured
     *
     * @return bool
     */
    public function getTfaConfigured(): ?bool;

    /**
     * Set tfa configured
     *
     * @param  bool $tfaConfigured
     * @return void
     */
    public function setTfaConfigured(bool $tfaConfigured): void;

    /**
     * Get tfa configuration required
     *
     * @return bool
     */
    public function getTfaConfigurationRequired(): ?bool;

    /**
     * Set tfa configuration required
     *
     * @param  bool $tfaConfigurationRequired
     * @return void
     */
    public function setTfaConfigurationRequired(bool $tfaConfigurationRequired): void;

    /**
     * Get tfa success
     *
     * @return bool
     */
    public function getTfaSuccess(): ?bool;

    /**
     * Set tfa success
     *
     * @param  bool $tfaSuccess
     * @return void
     */
    public function setTfaSuccess(bool $tfaSuccess): void;
}
