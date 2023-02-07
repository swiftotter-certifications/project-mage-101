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
