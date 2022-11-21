<?php

namespace Lof\CheckoutSuccessPage\Model\Config\Source;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Block
 */
class Block implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();
        $options = [];
        foreach ($cmsBlocks as $key => $item) {
            $options[$key]['value'] = $item->getId();
            $options[$key]['label'] = $item->getTitle();
        }
        return $options;
    }
}
