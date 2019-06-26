<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zilker\CustomRegistry\Registry\CurrentCategory;

/**
 * Class RegisterCurrentCategoryObserver
 * @package Zilker\CustomRegistry\Observer
 */
class RegisterCurrentCategoryObserver implements ObserverInterface
{
    /**
     * @var CurrentCategory $currentCategory
     */
    private $currentCategory;

    /**
     * RegisterCurrentCategoryObserver constructor.
     * @param CurrentCategory $currentCategory
     */
    public function __construct(CurrentCategory $currentCategory)
    {
        $this->currentCategory = $currentCategory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var CategoryInterface $category;
         */
        $category = $observer->getData('category');
        $this->currentCategory->setCategory($category);
    }
}
