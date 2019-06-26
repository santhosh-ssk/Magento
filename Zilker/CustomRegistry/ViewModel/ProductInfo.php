<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zilker\CustomRegistry\Registry\CurrentProduct;

/**
 * Class ProductInfo
 * @package Zilker\CustomRegistry\ViewModel
 */
class ProductInfo implements ArgumentInterface
{
    /**
     * @var CurrentProduct $currentProduct
     */
    private $currentProduct;

    /**
     * ProductInfo constructor.
     * @param CurrentProduct $currentProduct
     */
    public function __construct(CurrentProduct $currentProduct)
    {
        $this->currentProduct = $currentProduct;
    }

    /**
     * @return string|null
     */
    public function getProductName() : ?string
    {
        return (string) $this->currentProduct->getProduct()->getName();
    }
}
