<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Walmart\BopisLocationCheckIn\Api\CheckInHashProviderInterface;

class CheckInHashProvider implements CheckInHashProviderInterface
{
    /**
     * @var DeploymentConfig
     */
    private DeploymentConfig $deploymentConfig;

    /**
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        DeploymentConfig $deploymentConfig
    ) {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @inheritDoc
     */
    public function get(int $orderId): string
    {
        return sha1($orderId . $this->getCryptKey());
    }

    /**
     * @param string $hash
     * @param int    $orderId
     *
     * @return bool
     */
    public function isValid(string $hash, int $orderId): bool
    {
        return $this->get($orderId) === $hash;
    }

    /**
     * @return string
     * @throws FileSystemException
     * @throws RuntimeException
     */
    private function getCryptKey(): string
    {
        return $this->deploymentConfig->get('crypt/key');
    }
}
