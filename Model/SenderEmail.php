<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Model;

use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SenderEmail
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var StateInterface
     */
    public $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    public $transportBuilder;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * SenderEmail constructor.
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * @param $senderInfo
     * @param $receiverInfo
     * @param $storeID
     * @param $templateID
     * @param $emailTemplateVariables
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendMail($senderInfo, $receiverInfo, $storeID, $templateID, $emailTemplateVariables)
    {
        $this->inlineTranslation->suspend();
        $this->generateTemplate($senderInfo, $receiverInfo, $storeID, $templateID, $emailTemplateVariables);
        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            $this->logger->critical($e);
        }

        $this->inlineTranslation->resume();
    }

    /**
     * @param $senderInfo
     * @param $receiverInfo
     * @param $storeID
     * @param $templateID
     * @param $emailTemplateVariables
     * @return $this
     */
    public function generateTemplate($senderInfo, $receiverInfo, $storeID, $templateID, $emailTemplateVariables)
    {
        $template = $this->transportBuilder->setTemplateIdentifier($templateID)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeID
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
}
