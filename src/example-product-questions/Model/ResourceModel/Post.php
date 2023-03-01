<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model\ResourceModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Post extends AbstractDb
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        $connectionName = null
    ) {
        parent::__construct(
            $context, $connectionName
        );
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('productquestions_post', 'id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getProduct() && ($productId = $object->getProductId())) {
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
            $object->setProduct($product);
        }

        return parent::_beforeSave($object);
    }
}
