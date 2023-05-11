<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Plugin;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Vbdev\PersonalizedEmail\Model\Config\OrderPersonalizedEmail;
use Vbdev\PersonalizedEmail\Model\SenderEmail;

class OrderManagement
{
    private OrderPersonalizedEmail $orderPersonalizedEmailConfig;
    private SenderEmail $senderEmail;
    private ProductAttributeRepositoryInterface $attributeRepository;

    /**
     * @param OrderPersonalizedEmail $orderPersonalizedEmailConfig
     * @param SenderEmail $senderEmail
     * @param ProductAttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        OrderPersonalizedEmail              $orderPersonalizedEmailConfig,
        SenderEmail                         $senderEmail,
        ProductAttributeRepositoryInterface $attributeRepository
    ) {
        $this->orderPersonalizedEmailConfig = $orderPersonalizedEmailConfig;
        $this->senderEmail = $senderEmail;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface           $result
    ): OrderInterface {
        if (!$this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailEnabled() || !$this->canSendOrderPersonalizedEmailEmail($result)) {
            return $result;
        }
        $paramsEmail = $this->getParamsEmail($result);
        if ($paramsEmail) {
            $this->senderEmail->sendMail(
                $paramsEmail["senderInfo"],
                $paramsEmail["receiverInfo"],
                $paramsEmail["storeID"],
                $paramsEmail["templateID"],
                $paramsEmail["emailTemplateVariables"]
            );
        }
        return $result;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getAttributeCodeById(): string
    {
        return $this->attributeRepository->get($this->orderPersonalizedEmailConfig->getAttributeIdSelected())->getAttributeCode();
    }

    /**
     * @param OrderInterface $result
     * @return bool|string
     * @throws NoSuchEntityException
     */
    private function getProductId(OrderInterface $result)
    {
        $items = $result->getAllVisibleItems();
        $selectedAttributeCode = $this->getAttributeCodeById();
        foreach ($items as $item) {
            if (array_key_exists($selectedAttributeCode, $item->getProduct()->getAttributes())) {
                return $item->getProduct()->getId();
            }
        }
        return false;
    }

    /**
     * @param $order
     * @return mixed
     */
    private function getOrderStoreId($order)
    {
        return $order->getStore()->getStoreId();
    }

    /**
     * @param OrderInterface $result
     * @return bool
     * @throws NoSuchEntityException
     */
    private function canSendOrderPersonalizedEmailEmail(OrderInterface $result)
    {
        if (!$this->getProductId($result)) {
            return false;
        }

        return true;
    }

    /**
     * @param OrderInterface $result
     * @return array
     */
    private function getParamsEmail(OrderInterface $result): array
    {
        $emailTemplateVariables['order'] = $result;

        $senderInfo = [
            'name' => $this->orderPersonalizedEmailConfig->getStoreName(),
            'email' => $this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailStoreEmail()
        ];

        $receiverInfo = [
            'name' => $result->getCustomerName(),
            'email' => $result->getCustomerEmail()
        ];

        $storeID = $this->getOrderStoreId($result);

        $templateID = $this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailTemplateExample();

        return [
            "senderInfo" => $senderInfo,
            "receiverInfo" => $receiverInfo,
            "storeID" => $storeID,
            "templateID" => $templateID,
            "emailTemplateVariables" => $emailTemplateVariables
        ];
    }
}
