<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceFaasSync\Model\AttributeName;

class SetSourceAsOutOfSync implements ObserverInterface
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param Config                    $config
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        Config $config
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     *
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        /**
         * @var SourceInterface $source
        */
        $source = $observer->getData('source');

        $source->getExtensionAttributes()->setIsWmtBopisSynced(false);
        $this->sourceRepository->save($source);
    }
}
