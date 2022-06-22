<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class InvalidateSessionsButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        if ($this->getUserId()) {
            return [
                'label' => __('Invalidate All Sessions'),
                'class' => 'invalidate_sessions',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getInvalidateSessionsUrl() . '\', {"data": {}})',
                'sort_order' => 10,
                'aclResource' => 'Walmart_BopisStoreAssociate::user',
            ];
        }

        return [];
    }

    /**
     * Get invalidate sessions url.
     *
     * @return string
     */
    public function getInvalidateSessionsUrl(): string
    {
        return $this->getUrl('*/*/invalidatesessions', ['user_id' => $this->getUserId()]);
    }
}
