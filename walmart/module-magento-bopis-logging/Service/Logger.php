<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLogging\Service;

use Magento\Store\Model\ScopeInterface;
use Walmart\BopisLogging\Logger\Logger as LoggerModule;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    private const XML_PATH_BOPIS_CLIENT_DEBUG = 'bopis/logging/debug';

    /**
     * @var LoggerModule
     */
    private LoggerModule $logger;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * true if is set up (yes) on admin panel
     *
     * @var bool
     */
    private bool $isDebugEnable;

    /**
     * @param LoggerModule         $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LoggerModule $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->isDebugEnable = $this->isDebugEnabled();
    }

    /**
     * Check if logging is enabled
     *
     * @return bool
     */
    private function isDebugEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BOPIS_CLIENT_DEBUG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Register log if logging is enabled in admin panel
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->info($message, $context);
        }
    }

    /**
     * Register emergency log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function emergency($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->emergency($message, $context);
        }
    }

    /**
     * Register alert log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function alert($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->alert($message, $context);
        }
    }

    /**
     * Register critical log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function critical($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->critical($message, $context);
        }
    }

    /**
     * Register error log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function error($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->error($message, $context);
        }
    }

    /**
     * Register warning log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function warning($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->warning($message, $context);
        }
    }

    /**
     * Register notice log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function notice($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->notice($message, $context);
        }
    }

    /**
     * Register info log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function info($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->info($message, $context);
        }
    }

    /**
     * Register debug log if logging is enabled in admin panel
     *
     * @param  string $message
     * @param  array  $context
     * @return void|null
     */
    public function debug($message, array $context = [])
    {
        if ($this->isDebugEnable) {
            $this->logger->debug($message, $context);
        }
    }
}
