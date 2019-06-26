<?php
declare(strict_types=1);

namespace Zilker\CustomRegistry\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zilker\CustomRegistry\Registry\CurrentCategory;

/**
 * Class CategoryInfo
 * @package Zilker\CustomRegistry\ViewModel
 */
class CategoryInfo implements ArgumentInterface
{

    /**
     * @var CurrentCategory $cuurentCategory;
     */
    private $currentCategory;

    /**
     * CategoryInfo constructor.
     * @param CurrentCategory $currentCategory
     */
    public function __construct(CurrentCategory $currentCategory)
    {
        $this->currentCategory = $currentCategory;
    }

    /**
     * @return string
     */
    public function getCategoryName() : string
    {
        return (string) $this->currentCategory->getCategory()->getName();
    }
}
