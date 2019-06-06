<?php

namespace Zilker\MailerCode\Plugin\MailerCode\Product;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
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
     * @param Quote $subject
     * @param Product $product
     * @param null|float|DataObject $request
     * @param String|null $processMode
     * @return array
     */
    public function beforeAddProduct(
        Quote $subject,
        $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if ($request instanceof DataObject) {
            if ($request->hasData("mailercode")) {
                $entityId = $request->getData("mailercode");
                /**
                 * @var MailerCodeInterface $mailercode
                 */
                try {
                    $mailercode = $this->mailerCodeRepository->getById($entityId);
                    $minQuantity = $mailercode->getMinQuantity();
                    $qty = $request->getData('qty');
                    if ($qty >= $minQuantity) {
                        $request->setCustomPrice($mailercode->getPrice());
                    }
                } catch (Exception $e) {
                    $this->logger->info($e->getMessage());
                }
            }
        }
        return [$product, $request, $processMode];
    }
}
