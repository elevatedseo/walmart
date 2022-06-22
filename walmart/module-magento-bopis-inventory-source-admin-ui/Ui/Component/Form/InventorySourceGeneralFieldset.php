<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceAdminUi\Ui\Component\Form;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\Fieldset;
use Walmart\BopisApiConnector\Model\Config;

/**
 * Remove custom added fields and custom validation if general config is disabled
 */
class InventorySourceGeneralFieldset extends Fieldset
{
    private const CUSTOM_FIELDS = [
        'allow_ship_to_store',
        'use_as_shipping_source'
    ];

    private const CUSTOM_REQUIRED_VALIDATION_RULE_FIELDS = [
        'latitude',
        'longitude'
    ];

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
     * @return UiComponentInterface[]
     * @throws NoSuchEntityException
     */
    public function getChildComponents()
    {
        $fields = parent::getChildComponents();

        if (!$this->scopeConfig->isEnabled()) {
            foreach (self::CUSTOM_FIELDS as $field) {
                if (array_key_exists($field, $fields)) {
                    unset($fields[$field]);
                }
            }
            $fields = $this->unsetCustomValidationRules($fields);
        }

        return $fields;
    }

    /**
     * @param UiComponentInterface[] $fields
     * @return UiComponentInterface[]
     */
    private function unsetCustomValidationRules($fields)
    {
        foreach (self::CUSTOM_REQUIRED_VALIDATION_RULE_FIELDS as $field) {
            if (array_key_exists($field, $fields)) {
                $config = $fields[$field]->getData()['config'];
                unset($config['validation']['required-entry']);
                $fields[$field]->setData('config', $config);
            }
        }

        return $fields;
    }
}
