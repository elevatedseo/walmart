<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\Role\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * User role tab info
 */
class Info extends Generic implements TabInterface
{
    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel(): Phrase
    {
        return __('Role Info');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle(): Phrase
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Is tab hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * Before html rendering
     *
     * @return Info
     * @throws LocalizedException
     */
    public function _beforeToHtml(): self
    {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    /**
     * Form initializatiion
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _initForm(): void
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Role Information')]);

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Role Name'),
                'id' => 'name',
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField('role_id', 'hidden', ['name' => 'role_id', 'id' => 'role_id']);

        if ($this->getRole()) {
            $form->setValues($this->getRole()->getData());
        }

        $this->setForm($form);
    }
}
