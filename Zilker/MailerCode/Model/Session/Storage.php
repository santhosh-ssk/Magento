<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Model\Session;

use Magento\Framework\Session\Storage as SessionStorage;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Storage
 * @package Zilker\MailerCode\Model\Session
 */
class Storage extends SessionStorage
{

    /**
     * Storage constructor.
     * @param StoreManagerInterface $storeManager
     * @param string $namespace
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        $namespace = 'mailerCodeSession',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}
