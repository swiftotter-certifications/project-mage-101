<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Context as CustomerContext;

class PostForm implements ArgumentInterface
{
    private ?Product $product = null;
    private ?int $parentId = null;

    private Context $httpContext;
    private Registry $coreRegistry;
    private UrlInterface $url;

    public function __construct(
        Context $httpContext,
        Registry $coreRegistry,
        UrlInterface $url
    ) {
        $this->httpContext = $httpContext;
        $this->coreRegistry = $coreRegistry;
        $this->url = $url;
    }

    public function getActionUrl(): string
    {
        return $this->url->getUrl('productquestions/post/create');
    }

    public function customerIsLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    public function getProductId(): ?int
    {
        $product = $this->getProduct();
        return ($product) ? (int) $product->getId() : null;
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

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
