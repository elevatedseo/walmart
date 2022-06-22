<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Walmart\BopisStoreAssociateTfaApi\Api\Data\AssociateTfaConfigInterface;
use Walmart\BopisStoreAssociateTfa\Model\ResourceModel\AssociateTfaConfig as AssociateAssociateTfaConfigResourceModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * Associate Tfa Config Model
 */
class AssociateTfaConfig extends AbstractExtensibleModel implements AssociateTfaConfigInterface
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param Context                    $context
     * @param Registry                   $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory      $customAttributeFactory
     * @param EncryptorInterface         $encryptor
     * @param SerializerInterface        $serializer
     * @param AbstractResource|null      $resource
     * @param AbstractDb|null            $resourceCollection
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        EncryptorInterface $encryptor,
        SerializerInterface $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AssociateAssociateTfaConfigResourceModel::class);
    }

    /**
     * @return int
     */
    public function getConfigId(): int
    {
        return (int)$this->getData(self::CONFIG_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setConfigId(int $id): void
    {
        $this->setData(self::CONFIG_ID, $id);
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->getData(self::USER_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setUserId(int $id): void
    {
        $this->setData(self::USER_ID, $id);
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->getData(self::PROVIDER);
    }

    /**
     * @param  string $provider
     * @return void
     */
    public function setProvider(string $provider): void
    {
        $this->setData(self::PROVIDER, $provider);
    }

    /**
     * @return string[]
     */
    public function getEncodedConfig(): array
    {
        $config = $this->encryptor->decrypt($this->getData(self::ENCODED_CONFIG));

        return $config ? $this->serializer->unserialize($config) : [];
    }

    /**
     * @param  array $encodedConfig
     * @return void
     */
    public function setEncodedConfig(array $encodedConfig): void
    {
        $this->setData(self::ENCODED_CONFIG, $this->encryptor->encrypt($this->serializer->serialize($encodedConfig)));
    }
}
