<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Plugin\MailerCode\Item\Options;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use Psr\Log\LoggerInterface;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class RemoveMailerCode
 * @package Zilker\MailerCode\Plugin\MailerCode\Item\Options
 */
class RemoveMailerCode
{

    /**
     * @var Json $jsonSerializer
     */
    protected $jsonSerializer;

    /**
     * @var MailerCodeRepositoryInterface $mailerCodeRepository
     */
    protected $mailerCodeRepository;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var ProductRepositoryInterface $productRepository
     */
    protected $productRepository;

    /**
     * RemoveMailerCode constructor.
     * @param Json $jsonSerializer
     * @param MailerCodeRepositoryInterface $mailerCodeRepository
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Json $jsonSerializer,
        MailerCodeRepositoryInterface $mailerCodeRepository,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->mailerCodeRepository = $mailerCodeRepository;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Item $item
     * @param Item $target
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCompare(
        Item $item,
        Item $target
    ) {
        /**
         * @var Item $item
         */
        $item   = $this->removeMailerCodeOption($item)[0];

        /**
         * @var Item $target
         */
        $target = $this->removeMailerCodeOption($target);
        $mailerCodeId = $target[1];
        $productId = $target[2];
        $target = $target[0];
        if ($mailerCodeId) {
            try {
                $qty = $target->getQty() + $item->getQty();

                /**
                 * @var MailerCodeInterface $mailerCode
                 */
                $mailerCodeId = (int) $mailerCodeId;
                $mailerCode = $this->mailerCodeRepository->getById($mailerCodeId);
                if ($qty >= $mailerCode->getMinQuantity()) {
                    $item->setCustomPrice($mailerCode->getPrice());
                    $item->setOriginalCustomPrice($mailerCode->getPrice());
                }
            } catch (Exception $e) {
                $this->logger->info($e->getMessage());
            }
        } else {
            // remove mailer code price if applied
            $productId = (int) $productId;
            try {
                /** @var ProductInterface $product */
                $product = $this->productRepository->getById($productId);
                $item->setCustomPrice($product->getPrice());
                $item->setOriginalCustomPrice($product->getPrice());
            } catch (NoSuchEntityException $e) {
                $this->logger->info($e->getMessage());
            }
        }
        return [$target];
    }

    /**
     * @param Item $item
     * @return array
     */
    protected function removeMailerCodeOption(Item $item) : array
    {
        $mailercode = null;
        $productId  = null;

        /**
         * @var Option $mailerCodeOption
         */
        $mailerCodeOption = $item->getOptionByCode('info_buyRequest');
        if ($mailerCodeOption) {
            $value=$mailerCodeOption->getValue();
            $value = $this->jsonSerializer->unserialize($value);
            $productId = $value['product'];
            if (array_key_exists('mailercode', $value)) {
                $mailercode = $value['mailercode'];
                unset($value['mailercode']);
                $value = $this->jsonSerializer->serialize($value);
                $mailerCodeOption->setValue($value);
            }
            $item->saveItemOptions();
        }
        return [$item , $mailercode ,$productId];
    }
}
