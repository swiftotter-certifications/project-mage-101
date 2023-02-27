<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SwiftOtter\ProductQuestions\Model\Config;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\Collection as PostCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Registry;

class Questions implements ArgumentInterface
{
    private ?Product $product = null;

    private Config $config;
    private PostCollectionFactory $postCollectionFactory;
    private Registry $coreRegistry;

    public function __construct(
        Config $config,
        PostCollectionFactory $postCollectionFactory,
        Registry $coreRegistry
    ) {
        $this->config = $config;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function getHeading(): string
    {
        return $this->config->getHeading();
    }

    public function getQuestions(): array
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        /** @var PostCollection $posts */
        $posts = $this->postCollectionFactory->create();
        $posts->addProductIdFilter((int) $product->getId())
            ->addQuestionsOnlyFilter()
            ->addAnswers()
            ->addOrder('created_at', 'DESC');

        return $posts->getItems();
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
