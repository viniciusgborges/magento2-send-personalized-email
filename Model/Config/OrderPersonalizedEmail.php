<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class OrderPersonalizedEmail
{
    const XML_PERSONALIZED_EMAIL_ENABLED = 'sales_email/personalized_email/enabled';
    const XML_PERSONALIZED_EMAIL_SENDER = 'sales_email/personalized_email/sender_email';
    const XML_PERSONALIZED_EMAIL_TEMPLATE_EXAMPLE = 'sales_email/personalized_email/template_example';
    const XML_PATH_ATTRIBUTE_CODE = 'sales_email/personalized_email/custom_attribute_select';

    public ScopeConfigInterface $scopeConfig;

    /**
     * SenderEmailCustom constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailEnabled(): mixed
    {
        return $this->scopeConfig->getValue(
            self::XML_PERSONALIZED_EMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailEmailSender(): mixed
    {
        return $this->scopeConfig->getValue(
            self::XML_PERSONALIZED_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailTemplateExample(): mixed
    {
        return $this->scopeConfig->getValue(
            self::XML_PERSONALIZED_EMAIL_TEMPLATE_EXAMPLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get store config name
     * @return String
     */
    public function getStoreName(): string
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get store Config Email
     * @param $type
     * @return string
     */
    public function getStoreEmail($type): string
    {
        return $this->scopeConfig->getValue(
            "trans_email/ident_$type/email",
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return String
     */
    public function getOrderPersonalizedEmailStoreEmail(): string
    {
        return $this->getStoreEmail($this->getOrderPersonalizedEmailEmailSender());
    }

    /**
     * @return string
     */
    public function getAttributeIdSelected(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ATTRIBUTE_CODE, ScopeInterface::SCOPE_STORE);
    }
}
