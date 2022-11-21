<?php
namespace Lof\CheckoutSuccessPage\Block\Checkout;
use Lof\CheckoutSuccessPage\Helper\Data;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Widget\Helper\Conditions;

/**
 * Class RelatedProduct
 * @package Lof\CheckoutSuccessPage\Block\Checkout
 */
class RelatedProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var Conditions
     */
    protected $conditionsHelper;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var OrderFactory
     */
    private $_orderFactory;

    /**
     * FeaturedProducts constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Session $checkoutSession
     * @param Registry $coreRegistry
     * @param Conditions $conditionsHelper
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Session $checkoutSession,
        Registry $coreRegistry,
        Conditions $conditionsHelper,
        Data $helper,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_coreRegistry             = $coreRegistry;
        $this->helper                    = $helper;
        $this->conditionsHelper          = $conditionsHelper;
        $this->checkoutSession          = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function productWidget()
    {
        $productSkus = $this->getProductSkus();
        if (! $productSkus) {
            return '';
        }

        $numberProducts = $this->helper->getConfigRelated( 'number_of_products' ) ?: 10;
        $title = $this->helper->getConfigRelated('related_title') ?: __('Related Products');
        $productsBlock = $this->getLayout()->createBlock(\Magento\CatalogWidget\Block\Product\ProductsList::class);
        $productsBlock->setTitle($title);
        $productsBlock->setProductsCount($numberProducts);
        $productsBlock->setProductsPerPage($numberProducts);
        $productsBlock->setShowPager(0);
        $productsBlock->setTemplate('product/widget/content/grid.phtml');
        $conditions = [
            1      => [
                'type'       => \Magento\CatalogWidget\Model\Rule\Condition\Combine::class,
                'aggregator' => 'all',
                'value'      => '1',
                'new_child'  => '',
            ],
            '1--1' => [
                'type'      => \Magento\CatalogWidget\Model\Rule\Condition\Product::class,
                'attribute' => 'sku',
                'operator'  => '()',
                'value'     => implode(', ', $productSkus),
            ],
        ];
        $conditions = $this->conditionsHelper->encode($conditions);
        $productsBlock->setConditions($conditions);
        return $productsBlock->toHtml();
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
     * @return array
     */
    public function getProductSkus()
    {
        $sku = [];
        $items = $this->getOrder()->getAllItems();
        foreach ($items as $item) {
            $relatedProducts = $item->getProduct()->getRelatedProducts();
            foreach ($relatedProducts as $relatedProduct) {
                $sku[] = $relatedProduct->getSku();
            }
        }
        return $sku;
    }

    public function isEnable()
    {
        return $this->helper->getConfigRelated('enabled');
    }

    /**
     * @return array|mixed
     */
    public function getRelatedSortOrder()
    {
        return $this->helper->getConfigRelated('sort_order');
    }
}
