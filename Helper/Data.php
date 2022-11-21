<?php

namespace Lof\CheckoutSuccessPage\Helper;

use Magento\Backend\App\Config;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 *
 * @package Lof\CheckoutSuccessPage\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const CONFIG_MODULE_PATH = 'success_page';

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * @type array
     */
    protected $_data = [];

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $backendConfig;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var Block
     */
    private $blockResource;

    /**
     * AbstractData constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory $blockFactory
     * @param Block $blockResource
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory,
        Block $blockResource
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;
        $this->blockResource = $blockResource;
        parent::__construct($context);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigMessage($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/thanks_message' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigOrder($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/order_info' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigContinue($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/continue_button' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigCouponCode($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/coupon_code' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigSubscript($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/subscript' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigRelated($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/related' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigBlock($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/block' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigStyle($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/custom_style' . $code, $storeId);
    }

    /**
     * @param $field
     * @param null $scopeValue
     * @param string $scopeType
     *
     * @return array|mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($scopeValue === null && !$this->isArea()) {
            /** @var Config $backendConfig */
            if (!$this->backendConfig) {
                $this->backendConfig = $this->objectManager->get(\Magento\Backend\App\ConfigInterface::class);
            }

            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * @param string $area
     *
     * @return mixed
     */
    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            /** @var State $state */
            $state = $this->objectManager->get(\Magento\Framework\App\State::class);

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }

    /**
     * @param $identifier
     * @return false|\Magento\Cms\Model\Block
     */
    public function getStaticBlock($identifier)
    {
        try {
            $block = $this->blockFactory->create();
            $block->setStoreId($this->storeManager->getStore()->getId());
            $this->blockResource->load($block, $identifier);
            if (!$block->getId()) {
                throw new NoSuchEntityException(__('CMS Block with identifier "%1" does not exist.', $identifier));
            }
            return $block;
        } catch (\Exception $e) {
            //$this->logger->warning($e->getMessage());
        }
        return false;
    }

    /**
     * @param $identifier
     * @return Phrase|string
     */
    public function getCmsBlockContent($identifier)
    {
        $staticBlock = $this->getStaticBlock($identifier);

        if ($staticBlock && $staticBlock->isActive()) {
            return $staticBlock->getContent();
        }

        return __('Static block content not found');
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getIncrementId($storeId = null)
    {
        $code = '/order_number';
        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/frontend_preview' . $code, $storeId);
    }
}
