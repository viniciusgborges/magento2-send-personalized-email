<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;

class SenderEmail
{
    /**
     * @var StateInterface
     */
    public StateInterface $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    public TransportBuilder $transportBuilder;

    /**
     * @var LoggerInterface
     */
    public LoggerInterface $logger;

    /**
     * SenderEmail constructor.
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        StateInterface        $inlineTranslation,
        TransportBuilder      $transportBuilder,
        LoggerInterface       $logger
    ) {
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
     * @throws LocalizedException
     * @throws MailException
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
     * @throws MailException
     */
    public function generateTemplate($senderInfo, $receiverInfo, $storeID, $templateID, $emailTemplateVariables)
    {
        $this->transportBuilder->setTemplateIdentifier($templateID)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeID
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFromByScope($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
}
