<?php

namespace Zilker\MailerCode\Plugin\MailerCode\Product;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Processor;
use Psr\Log\LoggerInterface;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;

/**
 * Class Price
 * @package Zilker\MailerCode\Plugin\MailerCode\Product
 */
class Price
{
    /**
     * @var MailerCodeRepositoryInterface $mailerCodeRepository
     */
    protected $mailerCodeRepository;

    protected $logger;

    /**
     * Price constructor.
     * @param MailerCodeRepositoryInterface $mailerCodeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        MailerCodeRepositoryInterface $mailerCodeRepository,
        LoggerInterface $logger
    ) {
        $this->mailerCodeRepository = $mailerCodeRepository;
        $this->logger = $logger;
    }

    /**
     * @param Processor $processor
     * @param Item $item
     * @param DataObject $request
     * @param Product $candidate
     * @return array
     */
    public function beforePrepare(
        Processor $processor,
        Item $item,
        DataObject $request,
        Product $candidate
    ) {
        if ($request->hasData("mailercode")) {
            $entityId = $request->getData("mailercode");
            /**
             * @var MailerCodeInterface $mailercode
             */
            try {
                $mailercode = $this->mailerCodeRepository->getById($entityId);
                $minQuantity = $mailercode->getMinQuantity();
                $qty = $item->getQty() + $candidate->getCartQty();
                if ($qty >= $minQuantity) {
                    $this->logger->info('qty: ' . $qty);
                    $request->setCustomPrice($mailercode->getPrice());
                    $request=$request->unsetData(['mailercode']);
                }
            } catch (Exception $e) {
                $request->setCustomPrice($candidate->getPrice());
                $this->logger->info($e->getMessage());
            }
        }
        return [$item,$request,$candidate];
    }
}
