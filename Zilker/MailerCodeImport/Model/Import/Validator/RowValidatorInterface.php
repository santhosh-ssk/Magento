<?php

namespace Zilker\MailerCodeImport\Model\Import\Validator;

use Magento\Framework\Validator\ValidatorInterface;

interface RowValidatorInterface extends ValidatorInterface
{
    const ERROR_INVALID_TITLE = 'InvalidValueTITLE';
    const ERROR_ID_IS_EMPTY = 'Empty';

    /**
     * Initialize validator
     *
     * @param $context
     * @return $this
     */
    public function init($context);
}
