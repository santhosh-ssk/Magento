<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Plugin\Quote\Item\Remove;

use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class AdditionOptions
 * @package Zilker\MailerCode\Plugin\Quote\Item\Remove
 */
class AdditionOptions
{
    /**
     * @var Json $json
     */
    protected $json;

    /**
     * AdditionOptions constructor.
     * @param Json $json
     */
    public function __construct(Json $json)
    {
        $this->json = $json;
    }

    /**
     * @param Item $item
     * @param array $options1
     * @param array $options2
     * @return array
     */
    public function beforeCompareOptions(
        Item $item,
        array $options1,
        array $options2
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/viewmodel.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($options1));
        return [$options1,$options2];
    }
}
