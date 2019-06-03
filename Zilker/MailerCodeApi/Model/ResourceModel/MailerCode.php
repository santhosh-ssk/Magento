<?php
declare(strict_types=1);
namespace Zilker\MailerCodeApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class MailerCode
 * @package Zilker\MailerCodeApi\Model\ResourceModel
 */
class MailerCode extends AbstractDb
{
    /**#@+
     * Constants related to specific db layer
     */
    const TABLE_NAME_SOURCE_ITEM = 'zilker_product_mailer_code';
    const ID_FIELD_NAME = 'entity_id';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_SOURCE_ITEM, self::ID_FIELD_NAME);
    }
}
