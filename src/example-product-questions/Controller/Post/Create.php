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
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Manager;
use SwiftOtter\ProductQuestions\Command\InitPostFromRequest;
use SwiftOtter\ProductQuestions\Command\ValidatePost;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class Create implements ActionInterface, HttpPostActionInterface
{
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;
    private RequestInterface $request;
    private InitPostFromRequest $initPostFromRequest;
    private ValidatePost $validatePost;
    private Manager $messageManager;
    private PostResource $postResource;
    private Validator $formKeyValidator;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        RequestInterface $request,
        InitPostFromRequest $initPostFromRequest,
        ValidatePost $validatePost,
        Manager $messageManager,
        PostResource $postResource,
        Validator $formKeyValidator
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->initPostFromRequest = $initPostFromRequest;
        $this->validatePost = $validatePost;
        $this->messageManager = $messageManager;
        $this->postResource = $postResource;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->request->getParams();

        try {
            $post = $this->initPostFromRequest->execute($data, true);
            if ($this->validateFormKey()
                && $this->validatePost->execute($post, $this->request)) {
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

    /**
     * @throws InputException
     */
    private function validateFormKey(): bool
    {
        if (!$this->formKeyValidator->validate($this->request)) {
            throw new InputException(__('Invalid form data'));
        }
        return true;
    }
}
