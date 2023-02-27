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
use SwiftOtter\ProductQuestions\Command\InitPostFromRequest;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post as PostResource;

class Create implements ActionInterface, HttpPostActionInterface
{
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;
    private InitPostFromRequest $initPostFromRequest;
    private RequestInterface $request;
    private PostResource $postResource;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect,
        InitPostFromRequest $initPostFromRequest,
        RequestInterface $request,
        PostResource $postResource
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
        $this->initPostFromRequest = $initPostFromRequest;
        $this->request = $request;
        $this->postResource = $postResource;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        // TODO Validation

        $data = $this->request->getParams();

        try {
            $post = $this->initPostFromRequest->execute($data, true);
            $this->postResource->save($post);
            // TODO Success message
        } catch (LocalizedException $e) {
            // TODO Error message
        } catch (\Exception $e) {
            // TODO Error message
        }

        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($this->redirect->getRefererUrl());

        return $redirect;
    }
}
