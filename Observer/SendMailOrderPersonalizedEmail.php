<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Observer;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vbdev\PersonalizedEmail\Model\Config\OrderPersonalizedEmail;
use Vbdev\PersonalizedEmail\Model\SenderEmail;

class SendMailOrderPersonalizedEmail implements ObserverInterface
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer): static
    {
        if (!$this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailEnabled() || !$this->canSendOrderPersonalizedEmailEmail($observer)) {
            return $this;
        }
        $paramsEmail = $this->getParamsEmail($observer);
        if ($paramsEmail) {
            $this->senderEmail->sendMail(
                $paramsEmail["senderInfo"],
                $paramsEmail["receiverInfo"],
                $paramsEmail["storeID"],
                $paramsEmail["templateID"],
                $paramsEmail["emailTemplateVariables"]
            );
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    private function getOrder(Observer $observer): mixed
    {
        return $observer->getData('order');
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getAttributeCodeById(): string
    {
        return $this->attributeRepository->get($this->orderPersonalizedEmailConfig->getAttributeIdSelected())->getAttributeCode();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool|string
     * @throws NoSuchEntityException
     */
    private function getProductId(Observer $observer): bool|string
    {
        $items = $this->getOrder($observer)->getAllVisibleItems();
        foreach ($items as $item) {
            if (array_key_exists($this->getAttributeCodeById(), $item->getProduct()->getAttributes())) {
                return $item->getProduct()->getId();
            }
        }
        return false;
    }

    /**
     * @param $order
     * @return mixed
     */
    private function getOrderStoreId($order): mixed
    {
        return $order->getStore()->getStoreId();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @throws NoSuchEntityException
     */
    private function canSendOrderPersonalizedEmailEmail(Observer $observer): bool
    {
        if (!$this->getProductId($observer)) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    private function getParamsEmail(Observer $observer): array
    {
        $order = $this->getOrder($observer);

        $emailTemplateVariables['order'] = $order;

        $senderInfo = [
            'name' => $this->orderPersonalizedEmailConfig->getStoreName(),
            'email' => $this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailStoreEmail()
        ];

        $receiverInfo = [
            'name' => $order->getCustomerName(),
            'email' => $order->getCustomerEmail()
        ];

        $storeID = $this->getOrderStoreId($order);

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
