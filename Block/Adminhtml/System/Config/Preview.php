<?php
namespace Lof\CheckoutSuccessPage\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Url;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Preview
 * @package Lof\CheckoutSuccessPage\Block\Adminhtml\System\Config
 */
class Preview extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Lof_CheckoutSuccessPage::system/config/preview.phtml';

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * Preview constructor.
     * @param Context $context
     * @param Url $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $urlHelper,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        $html = '<td colspan="3">' . $this->_renderValue($element) . '</td>';

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element)
    {
        return $this->_getElementHtml($element);
    }

    /**
     * Get the grid and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * @return StoreInterface|null
     */
    public function getCurrentStore()
    {
        $form = $this->getElement()->getForm();
        if ($form->getStoreCode()) {
            $store = $this->_storeManager->getStore($form->getStoreCode());
        } elseif ($form->getWebsiteCode()) {
            $store = $this->_storeManager->getWebsite($form->getWebsiteCode())->getDefaultStore();
        } else {
            $store = $this->_storeManager->getDefaultStoreView();
        }

        return $store;
    }

    /**
     * @return string
     */
    public function getSuccessPageUrl()
    {
        $store = $this->getCurrentStore();
        $storeCode =  $store->getCode();

        $routeParams = [
            '_nosid' => true,
            '_query' => [
                '___store' => $storeCode
            ],
            'previewAccessCode' => '--previewAccessCode--'
        ];

        return $this->urlHelper->setScope($store->getId())->getUrl('checkout/onepage/success', $routeParams);
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getBaseUrl().'lof_checkoutsuccess/index/preview';
    }
}
