<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\Registry;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Class CurrentCategory
 * @package Zilker\CustomRegistry\Registry
 */
class CurrentCategory
{
    /**
     * @var CategoryInterface $category
     */
    private $category;

    /**
     * @var CategoryFactory $categoryFactory
     */
    private $categoryFactory;

    /**
     * CurrentCategory constructor.
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return CategoryInterface
     */
    public function getCategory(): CategoryInterface
    {
        return $this->category ?? $this->categoryFactory->create();
    }

    /**
     * @param CategoryInterface $category
     */
    public function setCategory(CategoryInterface $category): void
    {
        $this->category = $category;
    }
}
