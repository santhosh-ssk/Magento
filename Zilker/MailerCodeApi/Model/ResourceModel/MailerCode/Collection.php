<?php
declare(strict_types=1);

namespace Zilker\MailerCodeApi\Model\ResourceModel\MailerCode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zilker\MailerCodeApi\Model\MailerCode as MailerCodeModel;
use Zilker\MailerCodeApi\Model\ResourceModel\MailerCode as MailerCodeResourceModel;

/**
 * Class Collection
 * @package Zilker\MailerCodeApi\Model\ResourceModel\MailerCode
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(MailerCodeModel::class, MailerCodeResourceModel::class);
    }
}
