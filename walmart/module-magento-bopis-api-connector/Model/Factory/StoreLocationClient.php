<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model\Factory;

use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisSdk\StoreLocation;

class StoreLocationClient
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Client
     */
    private Client $factoryConnection;

    /**
     * @var StoreLocation|null
     */
    private ?StoreLocation $client = null;

    /**
     * @param Config $config
     * @param Client $factoryConnection
     */
    public function __construct(
        Config $config,
        Client $factoryConnection
    ) {
        $this->config = $config;
        $this->factoryConnection = $factoryConnection;
    }

    /**
     * @return StoreLocation
     * @throws NoSuchEntityException
     */
    public function create(): StoreLocation
    {
        if ($this->client === null) {
            $this->client = new StoreLocation(
                $this->factoryConnection->create(),
                $this->config->getEnvClientId(),
            );
        }

        return $this->client;
    }
}
