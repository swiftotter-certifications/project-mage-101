<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\Collection as PostCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class Questions implements ArgumentInterface
{
    private ?Product $product = null;

    private Registry $coreRegistry;
    private PostCollectionFactory $postCollectionFactory;

    public function __construct(
        Registry $coreRegistry,
        PostCollectionFactory $postCollectionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->postCollectionFactory = $postCollectionFactory;
    }

    public function getQuestionsContent(): array
    {
        /** @var PostCollection $postCollection */
        $postCollection = $this->postCollectionFactory->create();

        $product = $this->getProduct();
        $postCollection->addProductIdFilter((int) $product->getId())
            ->addQuestionsOnlyFilter()
            ->addAnswers();

        return $postCollection->getItems();
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
