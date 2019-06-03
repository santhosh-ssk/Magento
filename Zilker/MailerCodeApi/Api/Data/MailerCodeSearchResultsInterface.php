<?php
declare(strict_types=1);
namespace Zilker\MailerCodeApi\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MailerCodeSearchInterface
 * @package Zilker\MailerCodeApi\Api\Data
 */
interface MailerCodeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return MailerCodeInterface[]|null
     */
    public function getItems();
    /**
     * @param MailerCodeInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
