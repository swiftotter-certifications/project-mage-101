<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Controller\Post;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use SwiftOtter\ProductQuestions\Model\Post;
use SwiftOtter\ProductQuestions\Model\PostFactory;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class Create implements ActionInterface, HttpPostActionInterface
{
    private array $requiredData = ['customer_nickname', 'content', 'product_id'];

    private ?ProductInterface $product = null;
    private ?Post $parentPost = null;

    private RequestInterface $request;
    private Validator $formKeyValidator;
    private ManagerInterface $messageManager;
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;
    private CustomerSession $customerSession;
    private ProductRepositoryInterface $productRepository;
    private PostFactory $postFactory;
    private PostResource $postResource;

    public function __construct(
        RequestInterface $request,
        Validator $formKeyValidator,
        ManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        PostFactory $postFactory,
        PostResource $postResource

    ) {
        $this->request = $request;
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->validate()) {
            try {
                $post = $this->initPost();
                $this->postResource->save($post);
                $this->messageManager->addSuccessMessage(__('Your post has been saved'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('An unexpected error occurred'));
            }
        }

        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($this->redirect->getRefererUrl());
        return $redirect;
    }

    private function validate()
    {
        $data = $this->request->getParams();

        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Invalid form data'));
            return false;
        }

        if (!$this->getCustomerId()) {
            $this->messageManager->addErrorMessage(__('You must be logged in to leave a product question or answer'));
            return false;
        }

        $requiredDataMissing = false;
        foreach ($this->requiredData as $fieldName) {
            if (empty($data[$fieldName])) {
                $this->messageManager->addErrorMessage(__('"%1" is required', $fieldName));
                $requiredDataMissing = true;
            }
        }
        if ($requiredDataMissing) {
            return false;
        }

        try {
            $this->getProduct();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not find product'));
            return false;
        }

        try {
            $this->getParentPost();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not find product question'));
        }

        return true;
    }

    private function getCustomerId()
    {
        return $this->customerSession->getId();
    }

    /**
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function getProduct(): ProductInterface
    {
        if ($this->product === null) {
            $productId = $this->request->getParam('product_id');
            if (!$productId) {
                throw new InputException(__('No product ID'));
            }

            $this->product = $this->productRepository->getById($productId);
        }
        return $this->product;
    }

    /**
     * @throws NotFoundException
     */
    private function getParentPost(): ?Post
    {
        $parentId = $this->request->getParam('parent_id');
        if ($parentId && ($this->parentPost === null)) {
            /** @var Post $parentPost */
            $parentPost = $this->postFactory->create();
            $this->postResource->load($parentPost, $parentId);
            if (!$parentPost->getId()) {
                throw new NotFoundException(__('Post does not exist'));
            }

            $this->parentPost = $parentPost;
        }
        return $this->parentPost;
    }

    /**
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function initPost(): Post
    {
        /** @var Post $post */
        $post = $this->postFactory->create();

        $post->setData($this->request->getParams());
        $post->setCustomerId($this->getCustomerId());
        $post->setProduct($this->getProduct());

        return $post;
    }
}
