<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model\Factory;

use Magento\Framework\App\Cache;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisApiConnector\Model\Connection;
use Walmart\BopisLogging\Service\Logger;

class Client
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Connection|null
     */
    private ?Connection $connection = null;

    /**
     * @var Cache
     */
    private Cache $cache;

    private Logger $logger;

    /**
     * @param Cache  $cache
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        Cache $cache,
        Config $config,
        Logger $logger
    ) {
        $this->config = $config;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @return Connection
     * @throws NoSuchEntityException
     */
    public function create(): Connection
    {
        if (!$this->config->getServerUrl() || !$this->config->getTokenAuthUrl() || !$this->config->getEnvConsumerId()
            || !$this->config->getEnvConsumerSecret()) {
            throw new \Exception('Some configurations are missed. Save them and try again');
        }
        if ($this->connection === null) {
            $this->connection = new Connection(
                $this->cache,
                $this->config->getServerUrl(),
                $this->config->getTokenAuthUrl(),
                $this->config->getEnvConsumerId(),
                $this->config->getEnvConsumerSecret(),
                $this->config->isSandboxEnabled(),
                $this->logger
            );
        }

        return $this->connection;
    }
}
