<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\CheckoutSuccessPage\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

class EnableCustomerGroup implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var CollectionFactory
     */
    private $_groupCollectionFactory;
    /**
     * @var array
     */
    private $_options;

    public function __construct(CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
