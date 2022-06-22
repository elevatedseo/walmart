<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Magento\Framework\App\Cache;
use Psr\Log\LoggerInterface;
use Walmart\BopisSdk\AbstractConnection;
use Walmart\BopisSdk\Exception\PostTokenException;

class Connection extends AbstractConnection
{
    private const SDK_TOKEN_KEY = 'bopis_sdk_token';
    private const SDK_TOKEN_EXPIRY_TIME = 540;
    private const SANDBOX_ENV_ID = 'qa-int';

    /**
     * @var Cache|string
     */
    private Cache $cache;

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param Cache $cache
     * @param string $server
     * @param string $tokenAuthUrl
     * @param string $consumerId
     * @param string $consumerSecret
     * @param bool $enableSandbox
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Cache $cache,
        string $server,
        string $tokenAuthUrl,
        string $consumerId,
        string $consumerSecret,
        bool $enableSandbox,
        LoggerInterface $logger = null
    ) {
        parent::__construct(
            $server,
            $tokenAuthUrl,
            $consumerId,
            $consumerSecret,
            $enableSandbox ? self::SANDBOX_ENV_ID : null,
            $logger
        );

        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        $token = $this->cache->load(self::SDK_TOKEN_KEY);
        if (!$token) {
            try {
                $token = $this->generateToken();
                $this->cache->save($token, self::SDK_TOKEN_KEY, [], self::SDK_TOKEN_EXPIRY_TIME);
            } catch (PostTokenException $exception) {
                if ($this->logger) {
                    $this->logger->error(
                        __(
                            'There was a problem with generating the token',
                            [
                                'msg' => $exception->getMessage()
                            ]
                        )
                    );
                }
                return '';
            }
        }

        return $token;
    }
}
