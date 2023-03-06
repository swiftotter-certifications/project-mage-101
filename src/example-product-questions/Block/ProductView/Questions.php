<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Block\ProductView;

use Magento\Framework\View\Element\Template;
use SwiftOtter\ProductQuestions\ViewModel\PostForm;

class Questions extends Template
{
    public function getPostFormHtml(?int $parentId): string
    {
        /** @var Template $postFormBlock */
        $postFormBlock = $this->getLayout()->getBlock('questions.post_form');
        if (!$postFormBlock) {
            return '';
        }

        /** @var PostForm $postFormViewModel */
        $postFormViewModel = $postFormBlock->getData('view_model');
        if ($postFormViewModel && $parentId) {
            $postFormViewModel->setParentId($parentId);
        }

        return $postFormBlock->toHtml();
    }
}
