<?php
declare(strict_types=1);

namespace Zilker\MailerCode\ViewModel\Product;

use Exception;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;
use Zilker\MailerCode\Model\Session;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;

/**
 * Class MailerCode
 * @package Zilker\MailerCode
 */
class MailerCode implements ArgumentInterface
{
    /**
     * @var Session $mailerCodeSession
     */
    protected $mailerCodeSession;

    /**
     * @var LoggerInterface $looger
     */
    protected $logger;

    /**
     * MailerCode constructor.
     * @param Session $mailerCodeSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $mailerCodeSession,
        LoggerInterface $logger
    ) {
        $this->mailerCodeSession = $mailerCodeSession;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getSessionData()
    {
        try {
            /**
             * @var MailerCodeInterface $data;
             */
            $data = $this->mailerCodeSession->getMySession();
            $this->logger->info("Block Data: " . json_encode($data));
            $this->mailerCodeSession->clearStorage();
        } catch (Exception $e) {
            $this->logger->info("Error" . $e);
        }
        return  $data;
    }
}
