<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Block\Adminhtml\User\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\User\Edit\GenericButton;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateTfa\Model\ConfigProvider;

class DisableTfaButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProvider;

    /**
     * @var AssociateTfaConfigRepositoryInterface
     */
    private AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository;

    /**
     * @param Context                               $context
     * @param Session                               $session
     * @param ConfigProvider                        $configProvider
     * @param AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
     */
    public function __construct(
        Context $context,
        Session $session,
        ConfigProvider $configProvider,
        AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
    ) {
        parent::__construct($context, $session);
        $this->configProvider = $configProvider;
        $this->associateTfaConfigRepository = $associateTfaConfigRepository;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        if ($this->canShow()) {
            return [
                'label' => __('Disable TFA'),
                'class' => 'disable_tfa',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDisableTfaUrl() . '\', {"data": {}})',
                'sort_order' => 20,
                'aclResource' => 'Walmart_BopisStoreAssociate::user',
            ];
        }

        return [];
    }

    /**
     * Get Disable Tfa url.
     *
     * @return string
     */
    public function getDisableTfaUrl(): string
    {
        return $this->getUrl('*/*/disabletfa', ['user_id' => $this->getUserId()]);
    }

    /**
     * @return bool
     */
    private function canShow(): bool
    {
        if ($this->getUserId()) {
            try {
                $tfaConfig = $this->associateTfaConfigRepository->getByUserId($this->getUserId());

                return $this->configProvider->getIsTfaEnabled() && $tfaConfig->getEncodedConfig();
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }

        return false;
    }
}
