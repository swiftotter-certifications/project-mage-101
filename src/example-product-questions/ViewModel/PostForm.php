<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PostForm implements ArgumentInterface
{
    private ?Product $product = null;
    private ?int $parentId = null;

    private Registry $coreRegistry;
    private UrlInterface $url;
    private Context $httpContext;

    public function __construct(
        Registry $coreRegistry,
        UrlInterface $url,
        Context $httpContext
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->url = $url;
        $this->httpContext = $httpContext;
    }

    public function getActionUrl(): string
    {
        return $this->url->getUrl('productquestions/post/create');
    }

    public function customerIsLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    public function setParentId(?int $id): PostForm
    {
        $this->parentId = $id;
        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getProductId(): string
    {
        $product = $this->getProduct();
        return (string) $product->getId() ?? '';
    }

    public function getCssClass(): string
    {
        return ($this->getParentId()) ? 'answer' : 'question';
    }

    public function getContentLabel(): string
    {
        $label = ($this->getParentId()) ? __('Answer') : __('Question');
        return $label->render();
    }

    public function getSubmitLabel(): string
    {
        $label = ($this->getParentId()) ? __('Submit Answer') : __('Submit Question');
        return $label->render();
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
