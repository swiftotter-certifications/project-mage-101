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
use SwiftOtter\ProductQuestions\Model\Config;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\Collection as PostCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class Questions implements ArgumentInterface
{
    private ?Product $product = null;

    private Registry $coreRegistry;
    private PostCollectionFactory $postCollectionFactory;
    private Config $config;

    public function __construct(
        Registry $coreRegistry,
        PostCollectionFactory $postCollectionFactory,
        Config $config
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->config = $config;
    }

    public function getQuestionsContent(): array
    {
        /** @var PostCollection $postCollection */
        $postCollection = $this->postCollectionFactory->create();

        $product = $this->getProduct();
        $postCollection->addProductIdFilter((int) $product->getId())
            ->addQuestionsOnlyFilter()
            ->addAnswers()
            ->addOrder('updated_at', 'DESC');

        return $postCollection->getItems();
    }

    public function getHeading(): string
    {
        return $this->config->getProductPageHeading();
    }

    public function enabledOnProduct(): bool
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        $disabledAttr = $product->getCustomAttribute('enable_questions');
        return (!$disabledAttr) || (bool) $disabledAttr->getValue();
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
