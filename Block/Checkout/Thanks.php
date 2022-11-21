<?php

namespace Lof\CheckoutSuccessPage\Block\Checkout;

use Lof\CheckoutSuccessPage\Helper\Data;
use Magento\Framework\View\Element\Template;

/**
 * Class Thanks
 * @package Lof\CheckoutSuccessPage\Block\Checkout
 */
class Thanks extends Template
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Thanks constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->helper->isEnabled() && $this->helper->getConfigMessage('enabled')) {
            return parent::toHtml();
        }
    }

    /**
     * @return array|mixed
     */
    public function getMessage()
    {
        return $this->helper->getConfigMessage('message');
    }
}
