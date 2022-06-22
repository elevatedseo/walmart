<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model\Provider;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\GenerateImageException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google\TotpFactory;
use Walmart\BopisStoreAssociateTfa\Model\UserConfigManager;
use Base32\Base32;
use OTPHP\TOTPInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;

/**
 * Google authenticator engine
 */
class Google
{
    /**
     * Engine code
     */
    public const CODE = 'google';

    /**
     * @var UserConfigManager
     */
    private UserConfigManager $configManager;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var TotpFactory
     */
    private TotpFactory $totpFactory;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     * @param UserConfigManager     $configManager
     * @param TotpFactory           $totpFactory
     * @param EncryptorInterface    $encryptor
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UserConfigManager $configManager,
        TotpFactory $totpFactory,
        EncryptorInterface $encryptor
    ) {
        $this->configManager = $configManager;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->totpFactory = $totpFactory;
        $this->encryptor = $encryptor;
    }

    /**
     * Generate random secret
     *
     * @return string
     * @throws Exception
     */
    private function generateSecret(): string
    {
        $secret = random_bytes(128);
        // seed for iOS devices to avoid errors with barcode
        $seed = 'abcd';

        return preg_replace('/[^A-Za-z0-9]/', '', Base32::encode($seed . $secret));
    }

    /**
     * Get TOTP object
     *
     * @param AssociateUserInterface $user
     *
     * @return TOTPInterface
     * @throws NoSuchEntityException
     */
    private function getTotp(AssociateUserInterface $user): TOTPInterface
    {
        $secret = $this->getSecretCode($user);

        if (!$secret) {
            throw new NoSuchEntityException(__('Secret for user with ID#%1 was not found', $user->getUserId()));
        }

        $totp = $this->totpFactory->create($secret);

        return $totp;
    }

    /**
     * Get the secret code used for Google Authentication
     *
     * @param  AssociateUserInterface $user
     * @return string|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function getSecretCode(AssociateUserInterface $user): ?string
    {
        $config = $this->configManager->getUserConfiguration($user->getUserId());
        $arrayConfig = $config->getEncodedConfig();

        if (!isset($arrayConfig['secret'])) {
            $arrayConfig['secret'] = $this->generateSecret();
            $this->setSharedSecret($user->getUserId(), $arrayConfig['secret']);
            return $arrayConfig['secret'];
        }

        return $arrayConfig['secret'] ? $this->encryptor->decrypt($arrayConfig['secret']) : null;
    }

    /**
     * Set the secret used to generate OTP
     *
     * @param int    $userId
     * @param string $secret
     *
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function setSharedSecret(int $userId, string $secret): void
    {
        $this->configManager->setProviderConfig(
            $userId,
            static::CODE,
            $this->encryptor->encrypt($secret)
        );
    }

    /**
     * Get TFA provisioning URL
     *
     * @param AssociateUserInterface $user
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getProvisioningUrl(AssociateUserInterface $user): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        // @codingStandardsIgnoreStart
        $issuer = parse_url($baseUrl, PHP_URL_HOST);
        // @codingStandardsIgnoreEnd

        $totp = $this->getTotp($user);
        $totp->setLabel($user->getUsername());
        $totp->setIssuer($issuer);

        return $totp->getProvisioningUri();
    }

    /**
     * @param AssociateUserInterface $user
     * @param DataObject             $request
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function verify(AssociateUserInterface $user, DataObject $request): bool
    {
        $token = $request->getData('tfa_code');
        if (!$token) {
            return false;
        }

        $totp = $this->getTotp($user);

        return $totp->verify($token, null, null);
    }

    /**
     * Render TFA QrCode
     *
     * @param AssociateUserInterface $user
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws ValidationException
     * @throws GenerateImageException
     */
    public function getQrCodeAsPng(AssociateUserInterface $user): string
    {
        // @codingStandardsIgnoreStart
        $qrCode = new QrCode($this->getProvisioningUrl($user));
        $qrCode->setSize(400);
        $qrCode->setMargin(0);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLabelFontSize(16);
        $qrCode->setEncoding('UTF-8');

        $writer = new PngWriter();
        $pngData = $writer->writeString($qrCode);
        // @codingStandardsIgnoreEnd

        return $pngData;
    }
}
