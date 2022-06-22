<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api\Data;

/**
 * @api
 */
interface ResponseInterface
{
    const MESSAGE = 'message';
    const SUCCESS = 'success';

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set message
     *
     * @param string $message
     * @return ResponseInterface
     */
    public function setMessage(string $message): ResponseInterface;

    /**
     * Get success
     *
     * @return bool
     */
    public function getSuccess(): bool;

    /**
     * Set success
     *
     * @param bool $success
     * @return ResponseInterface
     */
    public function setSuccess(bool $success): ResponseInterface;
}
