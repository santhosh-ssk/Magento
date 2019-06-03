<?php
declare(strict_types=1);

namespace Zilker\MailerCodeApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;

interface MailerCodeRepositoryInterface
{
    /**
     * Save mailerCode.
     *
     * @param MailerCodeInterface $mailerCode
     * @return MailerCodeInterface
     */
    public function save(MailerCodeInterface $mailerCode);

    /**
     * Retrieve mailerCode.
     *
     * @param int $entityId
     * @return MailerCodeInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId) :MailerCodeInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return array|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * @param int $entityId
     * @return bool
     */
    public function delete(int $entityId) : bool;
}
