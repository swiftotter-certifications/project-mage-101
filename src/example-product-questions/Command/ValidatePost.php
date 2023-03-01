<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\ProductQuestions\Model\Post;
use SwiftOtter\ProductQuestions\Model\PostFactory;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class ValidatePost
{
    private array $requiredFields = ['customer_nickname', 'content', 'product_id'];
    private ProductRepositoryInterface $productRepository;
    private PostFactory $postFactory;
    private PostResource $postResource;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        PostFactory $postFactory,
        PostResource $postResource
    ) {
        $this->productRepository = $productRepository;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    /**
     * @throws AuthorizationException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(Post $post): bool
    {
        if (!$post->getCustomerId()) {
            throw new AuthorizationException(__('You must be logged in to submit a product question or answer.'));
        }

        $missingFields = [];
        foreach ($this->requiredFields as $fieldName) {
            if (!$post->getData($fieldName)) {
                $missingFields[] = $fieldName;
            }
        }
        if (!empty($missingFields)) {
            throw new InputException(
                __(
                    'The following fields are required: %1',
                    implode(', ', $missingFields)
                )
            );
        }

        if (!$post->getProduct()) {
            try {
                $this->productRepository->getById((int) $post->getProductId());
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__('The product associated with the post was not found.'));
            }
        }

        if (!$this->checkParentPost($post)) {
            throw new NoSuchEntityException(__('The associated product question was not found.'));
        }

        return true;
    }

    private function checkParentPost(Post $post): bool
    {
        $parentId = $post->getParentId();
        if (!$parentId) {
            return true;
        }

        /** @var Post $parentPost */
        $parentPost = $this->postFactory->create();
        $this->postResource->load($parentPost, $parentId);
        return (bool) $parentPost->getId();
    }
}
