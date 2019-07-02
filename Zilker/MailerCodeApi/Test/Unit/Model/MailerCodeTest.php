<?php
declare(strict_types=1);

namespace Zilker\MailerCodeApi\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Zilker\MailerCodeApi\Model\MailerCode;

/**
 * Class MailerCodeTest
 * @package Zilker\MailerCodeApi\Test\Unit\Model
 */
class MailerCodeTest extends TestCase
{
    /**
     * @var ObjectManager $objectManager
     */
    private $objectManager;

    /**
     * @var MailerCode $model
     */
    private $model;

    /**
     *
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->model = $this->objectManager->getObject(
            MailerCode::class,
            [
            ]
        );
    }

    /**
     * @param string|null $result
     * @param null $sku
     * @dataProvider getSkuDataProvider
     * @return void
     */
    public function testGetSku(?string $result, $sku = null) :void
    {
        if ($sku) {
            $this->model->setSku($sku);
        }
        $this->assertEquals($result, $this->model->getSku());
    }

    /**
     * @return array
     */
    public function getSkuDataProvider() : array
    {
        return [
            "correct" => ["abc","abc"],
            "nullable"=> [null]
        ];
    }
}
