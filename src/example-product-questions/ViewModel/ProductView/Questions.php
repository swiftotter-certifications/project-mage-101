<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\ViewModel\ProductView;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use SwiftOtter\ProductQuestions\Model\Config;

class Questions implements ArgumentInterface
{
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getHeading(): string
    {
        return $this->config->getHeading();
    }
}
