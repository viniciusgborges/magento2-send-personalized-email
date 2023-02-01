<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Vbdev\PersonalizedEmail\Model\Config\OrderPersonalizedEmail;
use Vbdev\PersonalizedEmail\Model\SenderEmail;

class SendMailOrderPersonalizedEmail implements ObserverInterface
{
    /**
     * @var OrderPersonalizedEmail
     */
    public $orderPersonalizedEmailConfig;

    /**
     * @var SenderEmail
     */
    public $senderEmail;

    /**
     * @var ProductFactory
     */
    public $productFactory;

    public $paramsEmail;

    public $orderPersonalizedEmailServiceType;

    public $productId;
    public ProductRepository $productRepository;

    /**
     * @param OrderPersonalizedEmail $orderPersonalizedEmailConfig
     * @param SenderEmail $senderEmail
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     */
    public function __construct(
        OrderPersonalizedEmail     $orderPersonalizedEmailConfig,
        SenderEmail                $senderEmail,
        ProductFactory             $productFactory,
        ProductRepository $productRepository
    ) {
        $this->orderPersonalizedEmailConfig = $orderPersonalizedEmailConfig;
        $this->senderEmail = $senderEmail;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailEnabled() || !$this->canSendEmail($observer)) {
            return $this;
        }

        if ($this->paramsEmail) {
            $this->senderEmail->sendMail(
                $this->paramsEmail["senderInfo"],
                $this->paramsEmail["receiverInfo"],
                $this->paramsEmail["storeID"],
                $this->paramsEmail["templateID"],
                $this->paramsEmail["emailTemplateVariables"]
            );
        }

        return $this;
    }

    /**
     * @param $observer
     * @return mixed
     */
    private function getOrder($observer)
    {
        return $observer->getData('order');
    }

    /**
     * @param $observer
     * @return bool
     */
    private function getProductId($observer)
    {
        $items = $this->getOrder($observer)->getAllVisibleItems();
        foreach ($items as $item) {
            $this->productId = $item->getProduct()->getId();
            return true;
        }
        return false;
    }

    /**
     * @return false|string
     */
    private function validateServiceType()
    {
//        $product = $this->productFactory->create()->load($this->productId);
        $product = $this->productRepository->getById($this->productId);
        if ($product->getData('example') != null) {
            return 'example';
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
     * @param $observer
     * @return bool
     */
    private function canSendOrderPersonalizedEmailEmail($observer)
    {
        if (!$this->getProductId($observer)) {
            return false;
        }

        if (!$this->orderPersonalizedEmailServiceType = $this->validateServiceType()) {
            return false;
        }

        return true;
    }

    /**
     * @param $observer
     * @return array
     */
    private function sendEmailOrderPersonalizedEmail($observer)
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

        $templateID = $this->getOrderPersonalizedEmailTemplateId();

        return [
            "senderInfo" => $senderInfo,
            "receiverInfo" => $receiverInfo,
            "storeID" => $storeID,
            "templateID" => $templateID,
            "emailTemplateVariables" => $emailTemplateVariables
        ];
    }

    /**
     * @return mixed|void
     */
    private function getOrderPersonalizedEmailTemplateId()
    {
        if ($this->orderPersonalizedEmailServiceType == 'example') {
            return $this->orderPersonalizedEmailConfig->getOrderPersonalizedEmailTemplateExample();
        }
    }

    /**
     * @param $observer
     * @return bool
     */
    private function canSendEmail($observer)
    {
        if ($this->canSendOrderPersonalizedEmailEmail($observer)) {
            $this->paramsEmail = $this->sendEmailOrderPersonalizedEmail($observer);
            return true;
        }
        return false;
    }
}
