<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Questions implements ArgumentInterface
{
    public function getHeading(): string
    {
        return 'Hello world from a new Product Questions module!';
    }
}
