<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Command;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\ProductQuestions\Model\Post;
use SwiftOtter\ProductQuestions\Model\PostFactory;

class InitPostFromRequest
{
    private PostFactory $postFactory;
    private Session $customerSession;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        PostFactory $postFactory,
        Session $customerSession,
        ProductRepositoryInterface $productRepository
    ) {
        $this->postFactory = $postFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    /**
     * @throws LocalizedException
     */
    public function execute(array $data, bool $attachProduct = false): Post
    {
        /** @var Post $post */
        $post = $this->postFactory->create();

        $post->setData($data);
        $post->setCustomerId($this->getCustomerId());

        if ($attachProduct) {
            $post->setProduct($this->getProduct($data));
        }

        return $post;
    }

    private function getCustomerId(): ?int
    {
        $customerId = $this->customerSession->getId();
        return ($customerId) ? (int) $customerId : null;
    }

    /**
     * @throws LocalizedException
     */
    private function getProduct(array $data): ?ProductInterface
    {
        if (!isset($data['product_id'])) {
            return null;
        }

        try {
            $product = $this->productRepository->getById((int)$data['product_id']);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('The product associated with the post was not found.'));
        }

        return $product;
    }
}
