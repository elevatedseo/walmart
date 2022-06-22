<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Ui\Component\Form;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\Fieldset;
use Walmart\BopisApiConnector\Model\Config;

class InventorySourceSyncStatusFieldset extends Fieldset
{
    /**
     * @var Config
     */
    private Config $scopeConfig;

    /**
     * @param ContextInterface $context
     * @param Config $scopeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Remove fieldset if general config is disabled
     */
    public function prepare()
    {
        if (!$this->scopeConfig->isEnabled()) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $this->setData('config', $config);
        }

        parent::prepare();
    }
}
