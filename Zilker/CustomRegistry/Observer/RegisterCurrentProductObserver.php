<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zilker\CustomRegistry\Registry\CurrentProduct;

/**
 * Class RegisterCurrentProductObserver
 * @package Zilker\CustomRegistry\Observer
 */
class RegisterCurrentProductObserver implements ObserverInterface
{

    /**
     * @var CurrentProduct $currentProduct;
     */
    private $currentProduct;

    /**
     * RegisterCurrentProductObserver constructor.
     * @param CurrentProduct $currentProduct
     */
    public function __construct(CurrentProduct $currentProduct)
    {
        $this->currentProduct = $currentProduct;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var ProductInterface $product
         */
        $product = $observer->getData('product');
        $this->currentProduct->setProduct($product);
    }
}
