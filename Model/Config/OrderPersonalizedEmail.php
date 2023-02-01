<?php
declare(strict_types=1);

namespace Wayne\PersonalizedEmail\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class OrderPersonalizedEmail
{
    const ORDER_PERSONALIZED_EMAIL_ENABLED = 'sales_email/personalized_email/enabled';
    const ORDER_PERSONALIZED_EMAIL_SENDER = 'sales_email/personalized_email/identity';
    const ORDER_PERSONALIZED_EMAIL_TEMPLATE_EXAMPLE = 'sales_email/personalized_email/template_example';
    const ORDER_PERSONALIZED_EMAIL_COPY_TO = 'sales_email/personalized_email/copy_to';
    const ORDER_PERSONALIZED_EMAIL_COPY_METHOD = 'sales_email/personalized_email/copy_method';

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

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
    public function getOrderPersonalizedEmailEnabled()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_PERSONALIZED_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailEmailSender()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_PERSONALIZED_EMAIL_SENDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailTemplateExample()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_PERSONALIZED_EMAIL_TEMPLATE_EXAMPLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailCopyTo()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_PERSONALIZED_EMAIL_COPY_TO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getOrderPersonalizedEmailCopyMethod()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_PERSONALIZED_EMAIL_COPY_METHOD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get store config name
     * @return String
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get store config Email
     * @return String
     */
    public function getStoreEmail($type)
    {
        return $this->scopeConfig->getValue(
            "trans_email/ident_$type/email",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return String
     */
    public function getOrderPersonalizedEmailStoreEmail()
    {
        return $this->getStoreEmail($this->getOrderPersonalizedEmailEmailSender());
    }
}
