<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Associate Tfa Config Model
 *
 * @api
 */
interface AssociateTfaConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const CONFIG_ID = 'config_id';
    public const USER_ID = 'user_id';
    public const PROVIDER = 'provider';
    public const ENCODED_CONFIG = 'encoded_config';

    /**
     * Get config ID
     *
     * @return int
     */
    public function getConfigId(): int;

    /**
     * Set config ID
     *
     * @param  int $id
     * @return void
     */
    public function setConfigId(int $id): void;

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set user ID
     *
     * @param  int $userId
     * @return void
     */
    public function setUserId(int $userId): void;

    /**
     * Get provider
     *
     * @return string|null
     */
    public function getProvider(): ?string;

    /**
     * Set provider
     *
     * @param  string $provider
     * @return void
     */
    public function setProvider(string $provider): void;

    /**
     * Get encoded config
     *
     * @return string[]
     */
    public function getEncodedConfig(): array;

    /**
     * Set encoded config
     *
     * @param  array $encodedConfig
     * @return void
     */
    public function setEncodedConfig(array $encodedConfig): void;
}
