<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\Registry;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class CurrentProduct
 * @package Zilker\CustomRegistry\Registry
 */
class CurrentProduct
{
    /**
     * @var ProductInterface $product
     */
    private $product;

    /**
     * @var ProductFactory $productFactory
     */
    private $productFactory;

    /**
     * CurrentProduct constructor.
     * @param ProductFactory $productFactory
     */
    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct(): ProductInterface
    {
        return $this->product ?? $this->productFactory->create();
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }
}
