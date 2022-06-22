<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Exception;
use Walmart\BopisApiConnector\Api\TokenInterface;
use Walmart\BopisApiConnector\Model\Factory\Client;
use Walmart\BopisLogging\Service\Logger;

class Token implements TokenInterface
{
    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var Client
     */
    private Factory\Client $clientFactory;

    /**
     * @param Client $clientFactory
     * @param Logger $logger
     */
    public function __construct(
        Client $clientFactory,
        Logger $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getToken(): ?string
    {
        try {
            return $this->clientFactory->create()->getToken();
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with generating the token',
                [
                'msg' => $exception->getMessage()
                ]
            );
        }

        return null;
    }
}
