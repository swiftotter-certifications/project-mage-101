<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const CONFIG_PATH_ENABLED = 'catalog/questions/enabled';
    const CONFIG_PATH_HEADING = 'catalog/questions/heading';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED, $scopeType, $scopeCode);
    }

    public function getHeading(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_HEADING, $scopeType, $scopeCode);
    }
}
