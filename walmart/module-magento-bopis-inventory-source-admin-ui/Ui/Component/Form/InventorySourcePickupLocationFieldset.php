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
 * Remove custom added fields and custom visibility if general config is disabled
 */
class InventorySourcePickupLocationFieldset extends Fieldset
{
    private const CUSTOM_FIELDS = [
        'store_pickup_enabled',
        'store_pickup_instructions',
        'curbside_enabled',
        'curbside_instructions',
        'pickup_lead_time',
        'pickup_time_label',
        'opening_hours',
        'checkin_experience'
    ];

    private const CUSTOM_HIDDEN_FIELDS = [
        'frontend_name',
        'frontend_description'
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
            $fields = $this->unsetHideAction($fields);
        }

        return $fields;
    }

    /**
     * Remove hidden field flag
     *
     * @param UiComponentInterface[] $fields
     * @return UiComponentInterface[]
     */
    private function unsetHideAction($fields)
    {
        foreach (self::CUSTOM_HIDDEN_FIELDS as $field) {
            if (array_key_exists($field, $fields)) {
                $config = $fields[$field]->getData()['config'];
                $config['visible'] = true;
                $fields[$field]->setData('config', $config);
            }
        }

        return $fields;
    }
}
