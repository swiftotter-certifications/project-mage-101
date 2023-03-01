<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Post extends AbstractModel implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class);
    }

    public function getIdentities()
    {
        $product = $this->getProduct();
        return ($product) ? $product->getIdentities() : [];
    }
}
