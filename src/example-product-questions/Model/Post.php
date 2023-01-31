<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Post extends AbstractModel implements IdentityInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $product = $this->getProduct();
        return ($product) ? $product->getIdentities() : [];
    }

    public function getProduct(): ?IdentityInterface
    {
        if (!$this->hasData('product') && $this->hasData('product_id')) {
            try {
                $product = $this->productRepository->getById($this->_getData('product_id'));
            } catch (\Exception $e) {
                $product = null;
            }
            $this->setData('product', $product);
        }
        return $this->_getData('product');
    }
}
