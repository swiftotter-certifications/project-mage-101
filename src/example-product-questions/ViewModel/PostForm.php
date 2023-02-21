<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Http\Context;

class PostForm implements ArgumentInterface
{
    private ?Product $product = null;

    private UrlInterface $url;
    private Registry $coreRegistry;
    private Context $httpContext;

    public function __construct(
        UrlInterface $url,
        Registry $coreRegistry,
        Context $httpContext
    ) {
        $this->url = $url;
        $this->coreRegistry = $coreRegistry;
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

    public function getProductId(): string
    {
        $product = $this->getProduct();
        return (string) $product->getId() ?? '';
    }

    private function getProduct(): ?Product
    {
        if ($this->product === null) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }
}
