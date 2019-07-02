<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Plugin\MailerCode\Item\Options;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use Psr\Log\LoggerInterface;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;

/**
 * Class RemoveMailerCode
 * @package Zilker\MailerCode\Plugin\MailerCode\Item\Options
 */
class RemoveMailerCode
{

    const ADDITIONAL_OPTIONS = 'additional_options';
    const INFO_BUY_REQUEST = 'info_buyRequest';
    const MAILERCODE = 'mailercode';

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
        $itemOptions = $item->getOptionsByCode();
        $itemFilterOptions = $this->removeMailerCodeOption($itemOptions);
        $itemFilterOption = $this->removeMailerCodeAdditionalOptions($itemFilterOptions[0], false);
        $item->setOptions($itemFilterOption);

        $targetOptions = $target->getOptionsByCode();
        $targetFilterOptions = $this->removeMailerCodeOption($targetOptions);
        $target->setOptions($this->removeMailerCodeAdditionalOptions($targetFilterOptions[0], false));

        $mailerCodeId = $targetFilterOptions[1];
        $productId = $targetFilterOptions[2];

        if ($mailerCodeId) {
            // set mailer code price
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
            // remove mailer code price if applied and set product price
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
     * removeMailerCodeOption function is used to remove mailercode from buyRequest
     * @param array $itemOptions
     * @return array
     */
    protected function removeMailerCodeOption(array $itemOptions) : array
    {
        $mailercode = null;
        $productId  = null;

        // remove mailer code option from buy request
        if (array_key_exists('info_buyRequest', $itemOptions)) {
            /**
             * @var Option $mailerCodeOption
             */
            $mailerCodeOption = $itemOptions[$this::INFO_BUY_REQUEST];
            $value=$mailerCodeOption->getValue();
            $value = $this->jsonSerializer->unserialize($value);
            $productId = $value['product'];
            if (array_key_exists('mailercode', $value)) {
                $mailercode = $value['mailercode'];
                unset($value['mailercode']);
                $mailerCodeOption->setValue($this->jsonSerializer->serialize($value));
            }
            $itemOptions['info_buyRequest'] = $mailerCodeOption;
        }
        return [$itemOptions , $mailercode ,$productId];
    }

    /**
     * @param Item $item
     * @param array $options1
     * @param array $options2
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCompareOptions(
        Item $item,
        array $options1,
        array $options2
    ) {
        $options1 = $this->removeMailerCodeAdditionalOptions($options1);
        return [$options1,$options2];
    }

    /**
     * @param array $options
     * @param bool $removeAdditionalOptions
     * @return array
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function removeMailerCodeAdditionalOptions(array $options, bool $removeAdditionalOptions = true) : array
    {
        try {
            if (array_key_exists($this::ADDITIONAL_OPTIONS, $options)) {
                $isEmpty = false;
                $index = null;
                $itr = 0;

                /**
                 * @var Option $option
                 */
                $option = $options['additional_options'];
                $values =$this->jsonSerializer->unserialize($option->getValue());
                $length = count($values);
                for ($i=0; $i<$length; $i++) {
                    if (array_key_exists('label', $values[$i])) {
                        if ($values[$i]['label'] == 'mailercode') {
                            $index = $itr;
                        }
                    }
                    $itr++;
                }

                // mailercode exist in item at index
                $this->logger->info('index: ' . $index . ' Values: ' . json_encode($values));
                if (is_integer($index) && $index>=0) {
                    array_splice($values, $index, 1);
                    if (count($values)==0) {
                        $isEmpty = true;
                    }
                }

                $option->setValue($this->jsonSerializer->serialize($values));
                $options['additional_options'] = $option;

                if ($isEmpty && $removeAdditionalOptions) {
                    unset($options['additional_options']);
                }
            }
        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
        }
        return $options;
    }
}
