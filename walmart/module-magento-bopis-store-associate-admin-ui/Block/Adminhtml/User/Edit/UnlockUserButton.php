<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\User\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;

class UnlockUserButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @param Context $context
     * @param Session $session
     * @param AssociateUserRepositoryInterface $associateUserRepository
     */
    public function __construct(
        Context $context,
        Session $session,
        AssociateUserRepositoryInterface $associateUserRepository
    ) {
        parent::__construct($context, $session);
        $this->associateUserRepository = $associateUserRepository;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        try {
            if (!$this->getUserId()) {
                return [];
            }

            $user = $this->associateUserRepository->get($this->getUserId());
            if ($user->getLockExpires()) {
                return [
                    'label' => __('Unlock User'),
                    'class' => 'unlock_user',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getUnlockUserUrl() . '\', {"data": {}})',
                    'sort_order' => 20,
                    'aclResource' => 'Walmart_BopisStoreAssociate::user',
                ];
            } else {
                return [];
            }
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }

    /**
     * Get Unlock User url.
     *
     * @return string
     */
    public function getUnlockUserUrl(): string
    {
        return $this->getUrl('*/*/unlockuser', ['user_id' => $this->getUserId()]);
    }
}
