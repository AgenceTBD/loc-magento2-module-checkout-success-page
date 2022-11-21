<?php
namespace Lof\CheckoutSuccessPage\Block\Checkout;

use Lof\CheckoutSuccessPage\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Details
 * @package Lof\CheckoutSuccessPage\Block\Checkout
 */
class Details extends \Magento\Sales\Block\Order\Totals
{
    /**
     * Checkout Session
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Customer Session
     * @var Session
     */
    protected $customerSession;

    /**
     * Sales Factory
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * Order Address
     * @var Renderer
     */
    protected $render;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Pricing Helper Data
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $formatPrice;

    /**
     * Order Details Constructor
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Session $customerSession
     * @param OrderFactory $orderFactory
     * @param Context $context
     * @param Data $helper
     * @param Renderer $render
     * @param Registry $registry
     * @param \Magento\Framework\Pricing\Helper\Data $formatPrice
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        Session $customerSession,
        OrderFactory $orderFactory,
        Context $context,
        Data $helper,
        Renderer $render,
        Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $formatPrice,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->render = $render;
        $this->helper = $helper;
        $this->formatPrice = $formatPrice;
    }

    /**
     * Get last order id
     * @return string
     */
    public function getOrder()
    {
        $isPreview = $this->getRequest()->getActionName() === 'preview';

        if ( $isPreview ) {
            $incrementId = $this->helper->getIncrementId();

            if ( ! $incrementId ) {
                $incrementId = $this->checkoutSession->getLastRealOrderId() ?: '000000001';
            }
        } else {
            $incrementId = $this->checkoutSession->getLastRealOrderId();
        }

        return  $this->_order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
    }

    /**
     * Render Block
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Format Price
     *
     * @param float $value
     * @return float
     */
    public function formatPrice($value)
    {
        return $this->formatPrice->currency($value, true, false);
    }

    /**
     * Get Re-Order
     * @return string
     */
    public function getReorder()
    {
        $order = $this->getOrder();
        $orderID = $order->getId();
        $reorder = $this->getBaseUrl() . 'sales/order/view/order_id/' . $orderID;
        return $reorder;
    }

    /**
     * Get Print Order
     * @return string
     */
    public function getPrint()
    {
        $order = $this->getOrder();
        $orderID = $order->getId();
        $print = $this->getBaseUrl() . 'sales/order/print/order_id/' . $orderID;
        return $print;
    }

    /**
     * Format Shipping Address
     * @return string
     */
    public function formatShipping()
    {
        $order = $this->getOrder();
        if ($order->getShippingAddress()) {
            return $this->render->format($order->getShippingAddress(), 'html');
        }
        return false;
    }

    /**
     * Get Enable|Disable
     * @return bool
     */
    public function isEnableDetails()
    {
        return $this->helper->isEnabled();
    }

    /**
     * Get Enable|Disable
     * @return bool
     */
    public function isEnable()
    {
        return $this->helper->getConfigOrder('enabled');
    }

    /**
     * Get Customer Id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * Format Billing Address
     * @return string
     */
    public function formatBilling()
    {
        $order = $this->getOrder();
        return $this->render->format($order->getBillingAddress(), 'html');
    }

    /**
     * Format date
     *
     * @param null $date
     * @param int $format
     * @param bool $showTime
     * @param null $timezone
     * @param string $pattern
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null,
        $pattern = 'd MMM Y'
    ) {
        $date = $date instanceof \DateTimeInterface;
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone,
            $pattern
        );
    }

    /**
     * Return Opptions Configurable Product
     *
     * @param object $item
     * @return array
     */
    public function getItemOptions($item)
    {
        $result = [];
        $option = $item->getProductOptions();
        if ($option) {
            if (isset($option['options'])) {
                $result = array_merge($result, $option['options']);
            }
            if (isset($option['additional_options'])) {
                $result = array_merge($result, $option['additional_options']);
            }
            if (isset($option['attributes_info'])) {
                $result = array_merge($result, $option['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Return Opptions Bundle Product
     *
     * @param object $item
     * @return array
     */
    public function getBundleItemOptions($item)
    {
        $result = [];
        $option = $item->getProductOptions();
        if ($option) {
            if (isset($option['bundle_options'])) {
                $result = array_merge($result, $option['bundle_options']);
            }
        }
        return $result;
    }
    /**
     * Get item product
     *
     * @return \Magento\Catalog\Model\Product
     * @codeCoverageIgnore
     */
    public function getProduct($item)
    {
        return $item->getProduct();
    }
    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl($item)
    {
        $product = $this->getProduct($item);
        $option = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }
        return $product->getUrlModel()->getUrl($product);
    }

    /**
     * @return array|mixed
     */
    public function isEnableOrderStatusDetails()
    {
        return $this->helper->getConfigOrder('show_order_status');
    }

    /**
     * @return array|mixed
     */
    public function isEnableShippingAddressDetails()
    {
        return $this->helper->getConfigOrder('show_detail_address');
    }

    /**
     * @return array|mixed
     */
    public function isEnableShippingMethodDetails()
    {
        return $this->helper->getConfigOrder('show_shipping_methods');
    }

    /**
     * @return array|mixed
     */
    public function isEnableBillingAddressDetails()
    {
        return $this->helper->getConfigOrder('show_billing_address');
    }

    /**
     * @return array|mixed
     */
    public function isEnablePaymentMethodDetails()
    {
        return $this->helper->getConfigOrder('show_payment_methods');
    }

    /**
     * @return array|mixed
     */
    public function isEnableOrderProductDetails()
    {
        return $this->helper->getConfigOrder('show_order_details');
    }

    /**
     * @return array|mixed
     */
    public function showProductThumbnail()
    {
        return $this->helper->getConfigOrder('show_product_thumbnail');
    }

    /**
     * @return array|mixed
     */
    public function canViewReorder()
    {
        return $this->helper->getConfigOrder('show_reorder_button');
    }

    /**
     * @return array|mixed
     */
    public function canViewPrint()
    {
        return $this->helper->getConfigOrder('show_print_button');
    }

    /**
     * @return array|mixed
     */
    public function getButtonTextColor()
    {
        return $this->helper->getConfigStyle('button_text_color');
    }

    /**
     * @return array|mixed
     */
    public function getButtonBgColor()
    {
        return $this->helper->getConfigStyle('button_background_color');
    }

    /**
     * @return array|mixed
     */
    public function getTitleBgColor()
    {
        return $this->helper->getConfigStyle('title_background_color');
    }

    /**
     * @return array|mixed
     */
    public function getTitleColor()
    {
        return $this->helper->getConfigStyle('title_text_color');
    }

    /**
     * @return array|mixed
     */
    public function getTitleBorderColor()
    {
        return $this->helper->getConfigStyle('title_border_color');
    }

    /**
     * @return array|mixed
     */
    public function getMessageColor()
    {
        return $this->helper->getConfigStyle('message_color');
    }

    /**
     * @return array|mixed
     */
    public function getMessageBgColor()
    {
        return $this->helper->getConfigStyle('message_bg_color');
    }

    /**
     * @return array|mixed
     */
    public function getCouponColor()
    {
        return $this->helper->getConfigStyle('coupon_code_text_color');
    }

    /**
     * @return array|mixed
     */
    public function getCouponBgColor()
    {
        return $this->helper->getConfigStyle('coupon_code_bg_color');
    }

    /**
     * @return array|mixed
     */
    public function getCouponBorderColor()
    {
        return $this->helper->getConfigStyle('coupon_code_border_color');
    }

    /**
     * @return array|mixed
     */
    public function getContinueSortOrder()
    {
        return $this->helper->getConfigContinue('sort_order');
    }
}
