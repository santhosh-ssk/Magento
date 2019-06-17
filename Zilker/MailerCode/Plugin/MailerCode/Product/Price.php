<?php

namespace Zilker\MailerCode\Plugin\MailerCode\Product;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
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

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var Json $json
     */
    protected $json;

    /**
     * @var DataObject $data
     */
    protected $data;

    /**
     * Price constructor.
     * @param MailerCodeRepositoryInterface $mailerCodeRepository
     * @param LoggerInterface $logger
     * @param Json $json
     * @param DataObject $data
     */
    public function __construct(
        MailerCodeRepositoryInterface $mailerCodeRepository,
        LoggerInterface $logger,
        Json $json,
        DataObject $data
    ) {
        $this->mailerCodeRepository = $mailerCodeRepository;
        $this->logger = $logger;
        $this->json = $json;
        $this->data = $data;
    }

    /**
     * @param Processor $processor
     * @param Item $item
     * @param DataObject $request
     * @param Product $candidate
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function beforePrepare(
        Processor $processor,
        Item $item,
        DataObject $request,
        Product $candidate
    ) {
        $mailercodeId = null;
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
                    $item->setOriginalCustomPrice($mailercode->getPrice());
                    $item->setCustomPrice($mailercode->getPrice());
                    $request=$request->unsetData(['mailercode']);
                    $mailercodeId = $entityId;
                }
            } catch (Exception $e) {
                $item->setOriginalCustomPrice($candidate->getPrice());
                $item->setCustomPrice($candidate->getPrice());
                $this->logger->info($e->getMessage());
            }
        } else {
            // change price to product price if mailercode is not applied
            $item->setOriginalCustomPrice($candidate->getPrice());
            $item->setCustomPrice($candidate->getPrice());
        }

        $additionalOptions = [];
        $flag = true;
        if ($additionalOption = $item->getOptionByCode('additional_options')) {
            $additionalOptions = (array) $this->json->unserialize($additionalOption->getValue());
            $length = count($additionalOptions);
            for ($i=0; $i<$length; $i++) {
                $option = $additionalOptions[$i];
                if (array_key_exists('label', $option) && $option['label'] == 'mailercode') {
                    $additionalOptions[$i]['value'] = $mailercodeId;
                    $flag = false;
                }

            }

        }

        //adds additional data if does not exist
        if ($flag) {
            $additionalOptions[] = [
                'label' => 'mailercode',
                'value' => $mailercodeId
            ];
        }

        $this->data->setData([
            'code' => 'additional_options',
            'value' => $this->json->serialize($additionalOptions)
        ]);
        $item->addOption($this->data->getData());

        return [$item,$request,$candidate];
    }
}
