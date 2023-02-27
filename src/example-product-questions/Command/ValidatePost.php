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

class ValidatePost
{
    private array $requiredFields = ['customer_nickname', 'content', 'product_id'];
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
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

        return true;
    }
}
