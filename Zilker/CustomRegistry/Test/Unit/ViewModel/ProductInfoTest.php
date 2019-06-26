<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\Test\Unit\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Zilker\CustomRegistry\Registry\CurrentProduct;
use Zilker\CustomRegistry\ViewModel\ProductInfo;

/**
 * Class ProductInfoTest
 * @package Zilker\CustomRegistry\Test\Unit\ViewModel
 */
class ProductInfoTest extends TestCase
{
    /**
     * @var CurrentProduct $currentProduct
     */
    private $currentProduct;

    /**
     * @var ProductInfo $productInfo
     */
    private $productInfo;

    /**
     * @var ProductInterface $product;
     */
    private $product;

    /**
     * @throws ReflectionException
     */
    public function setUp()
    {
        $this->currentProduct = $this->createMock(CurrentProduct::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->productInfo = new ProductInfo(
            $this->currentProduct
        );
    }

    /**
     * @dataProvider productNameDataProvider
     */
    public function testGetProductName(string $result, string $name = null)
    {
        if ($name) {
            $this->product->setName($name);
            $this->currentProduct->setProduct($this->product);
        }
        $this->assertEquals($result, $this->productInfo->getProductName());
    }

    public function productNameDataProvider() : array
    {
        return [
            "TestCase 1" => ["abc","abc"],
            "Nullable"   => [""]
        ];
    }
}
