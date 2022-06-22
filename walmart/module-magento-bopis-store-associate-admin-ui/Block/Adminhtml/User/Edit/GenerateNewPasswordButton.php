<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class GenerateNewPasswordButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        if ($this->getUserId()) {
            return [
                'label' => __('Generate New Password'),
                'class' => 'generate_new_password',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getGenerateNewPasswordUrl() . '\', {"data": {}})',
                'sort_order' => 20,
                'aclResource' => 'Walmart_BopisStoreAssociate::user',
            ];
        }

        return [];
    }

    /**
     * Get generate new password url.
     *
     * @return string
     */
    public function getGenerateNewPasswordUrl(): string
    {
        return $this->getUrl('*/*/generatenewpassword', ['user_id' => $this->getUserId()]);
    }
}
