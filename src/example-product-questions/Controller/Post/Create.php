<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Controller\Post;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;

class Create implements ActionInterface, HttpPostActionInterface
{
    private RedirectFactory $redirectFactory;
    private RedirectInterface $redirect;

    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirect
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirect = $redirect;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($this->redirect->getRefererUrl());

        return $redirect;
    }
}
