<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button;

/**
 * Buttons block
 */
class Buttons extends Template
{
    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout(): self
    {
        $this->getToolbar()->addChild(
            'backButton',
            Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                'class' => 'back'
            ]
        );

        if ((int)$this->getRequest()->getParam('role_id')) {
            $this->getToolbar()->addChild(
                'deleteButton',
                Button::class,
                [
                    'label' => __('Delete Role'),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getUrl(
                        '*/*/delete',
                        ['role_id' => $this->getRequest()->getParam('role_id')]
                    ) . '\', {data: {}})',
                    'class' => 'delete'
                ]
            );
        }

        $this->getToolbar()->addChild(
            'saveButton',
            Button::class,
            [
                'label' => __('Save Role'),
                'class' => 'save primary save-role',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#role-edit-form']],
                ]
            ]
        );
        return parent::_prepareLayout();
    }

    /**
     * Get back button html
     *
     * @return string
     */
    public function getBackButtonHtml(): string
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * Get save button html
     *
     * @return string
     */
    public function getSaveButtonHtml(): string
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * Get delete button html
     *
     * @return string
     */
    public function getDeleteButtonHtml(): string
    {
        if ((int)$this->getRequest()->getParam('role_id') == 0) {
            return '';
        }
        return $this->getChildHtml('deleteButton');
    }
}
