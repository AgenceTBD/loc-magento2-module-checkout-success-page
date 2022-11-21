<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\CheckoutSuccessPage\Model\Config\Source;

use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory;

class CouponCode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $_salesRuleCoupon;
    private $_options;

    public function __construct(
        CollectionFactory $salesRuleCoupon
    ) {
        $this->_salesRuleCoupon = $salesRuleCoupon;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $saleCollection = $this->_salesRuleCoupon->create()->loadData();
        foreach ($saleCollection as $key => $item) {
            $options[$key]['value'] = $item->getCode();
            $options[$key]['label'] = $item->getCode();
        }
        return $options;
    }
}
