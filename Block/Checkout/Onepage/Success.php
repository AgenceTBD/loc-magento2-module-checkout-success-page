<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\CheckoutSuccessPage\Block\Checkout\Onepage;

use Magento\Sales\Model\Order;
/**
 * One page checkout success page
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Lof\CheckoutSuccessPage\Helper\Data
     */
    protected $helperData;

    /**
     * Success constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Sales\Model\Order\Config                $orderConfig
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param \Lof\CheckoutSuccessPage\Helper\Data             $helperData
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Lof\CheckoutSuccessPage\Helper\Data $helperData,
        array $data = []
    )
    {
        $this->helperData = $helperData;
        parent::__construct( $context, $checkoutSession, $orderConfig, $httpContext, $data );
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ( ! $this->helperData->isEnabled() ) {
            return parent::_toHtml();
        }

        return '';
    }
}
