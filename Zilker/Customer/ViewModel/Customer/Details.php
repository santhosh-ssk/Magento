<?php
declare(strict_types=1);
namespace Zilker\Customer\ViewModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Details
 * @package Zilker\Customer\ViewModel\Customer
 */
class Details implements ArgumentInterface
{
    private $customerSession;

    /**
     * Details constructor.
     * @param Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool
     */
    public function isLoggedIn() : bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return Customer
     */
    public function getCustomer() : ?Customer
    {
        if ($this->isLoggedIn()) {
            return $this->customerSession->getCustomer();
        } else {
            return null;
        }
    }
}
