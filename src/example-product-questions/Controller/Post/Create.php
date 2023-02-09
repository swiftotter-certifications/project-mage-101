<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Controller\Post;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Manager;
use SwiftOtter\ProductQuestions\Command\InitPostFromRequest;
use SwiftOtter\ProductQuestions\Command\ValidatePostFromRequest;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class Create implements ActionInterface, HttpPostActionInterface
{
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;
    private RequestInterface $request;
    private InitPostFromRequest $initPostFromRequest;
    private ValidatePostFromRequest $validatePost;
    private Manager $messageManager;
    private PostResource $postResource;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        RequestInterface $request,
        InitPostFromRequest $initPostFromRequest,
        ValidatePostFromRequest $validatePost,
        Manager $messageManager,
        PostResource $postResource
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->initPostFromRequest = $initPostFromRequest;
        $this->validatePost = $validatePost;
        $this->messageManager = $messageManager;
        $this->postResource = $postResource;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->request->getParams();

        try {
            $post = $this->initPostFromRequest->execute($data, true);
            if ($this->validatePost->execute($post, $this->request)) {
                $this->postResource->save($post);
                $this->messageManager->addSuccessMessage(__('Thank you for your submission!'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An unexpected error occurred'));
        }

        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($this->redirect->getRefererUrl());

        return $redirect;
    }
}
