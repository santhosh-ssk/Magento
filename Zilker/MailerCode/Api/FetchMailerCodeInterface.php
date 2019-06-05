<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Api;

use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;

/**
 * Interface FetchMailerCodeInterface
 * @package Zilker\MailerCode\Api
 */
interface FetchMailerCodeInterface
{
    /**
     * @param string $searchCode
     * @return MailerCodeInterface|null
     */
    public function getBySearchCode(string $searchCode) : ?MailerCodeInterface;
}
