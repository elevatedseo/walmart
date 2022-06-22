<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfaApi\Api\Data;

/**
 * Represents Google Configure response data
 *
 * @api
 */
interface GoogleConfigureResponseInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const QR_BASE64_IMAGE = 'qr_base64_image';
    public const SECRET_CODE = 'secret_code';

    /**
     * Get qr base64 image
     *
     * @return string
     */
    public function getQrBase64Image(): ?string;

    /**
     * Set qr base64 image
     *
     * @param  string $qrBase64Image
     * @return void
     */
    public function setQrBase64Image(string $qrBase64Image): void;

    /**
     * Get secret code
     *
     * @return string
     */
    public function getSecretCode(): ?string;

    /**
     * Set secret code
     *
     * @param  string $secretCode
     * @return void
     */
    public function setSecretCode(string $secretCode): void;
}
