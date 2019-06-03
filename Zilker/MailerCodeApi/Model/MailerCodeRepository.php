<?php
declare(strict_types=1);

namespace Zilker\MailerCodeApi\Model;

use Exception;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;
use Zilker\MailerCodeApi\Model\ResourceModel\MailerCode as MailerCodeResource;
use Zilker\MailerCodeApi\Model\ResourceModel\MailerCode\CollectionFactory;
use Zilker\MailerCodeApi\Model\ResourceModel\MailerCode\Collection;

/**
 * Class MailerCodeRepository
 * @package Zilker\MailerCodeApi\Model
 */
class MailerCodeRepository implements MailerCodeRepositoryInterface
{
    /**
     * @var MailerCodeResource
     */
    private $mailerCodeResource;

    /**
     * @var MailerCodeFactory
     */
    private $mailerCodeFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var MailerCodeSearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     * MailerCodeRepository constructor.
     * @param MailerCodeResource $mailerCodeResource
     * @param MailerCodeFactory $mailerCodeFactory
     * @param CollectionFactory $collection
     * @param MailerCodeSearchResultsFactory $searchInterface
     */
    public function __construct(
        MailerCodeResource $mailerCodeResource,
        MailerCodeFactory $mailerCodeFactory,
        CollectionFactory $collection,
        MailerCodeSearchResultsFactory $searchInterface
    ) {
        $this->mailerCodeResource   = $mailerCodeResource;
        $this->mailerCodeFactory    = $mailerCodeFactory;
        $this->collectionFactory    = $collection;
        $this->searchResultsFactory = $searchInterface;
    }

    /**
     * @inheritDoc
     * @throws AlreadyExistsException
     */
    public function save(MailerCodeInterface $mailerCode)
    {
        $this->mailerCodeResource->save($mailerCode);
        return $mailerCode;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $entityId) : MailerCodeInterface
    {
        $mailerCode = $this->mailerCodeFactory->create();
        $this->mailerCodeResource->load($mailerCode, $entityId);
        if (!$mailerCode->getEntityID()) {
            throw new NoSuchEntityException();
        }
        return $mailerCode;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $collection = $this->collectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        /**
         * @var SortOrder $sortOrder;
         */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                $sortOrder->getDirection()
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $mailerCodes = [];
        foreach ($collection as $mailerCode) {
            $mailerCodes[] = $mailerCode;
        }
        $searchResults->setItems($mailerCodes);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->getItems();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(int $entityId): bool
    {
        $mailerCode = $this->mailerCodeFactory->create();
        $mailerCode->setEntityId($entityId);
        if ($this->mailerCodeResource->delete($mailerCode)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param FilterGroup $group
     * @param Collection $collection
     */
    private function addFilterGroupToCollection(FilterGroup $group, Collection $collection)
    {
        $fields = [];
        $conditions = [];

        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $field = $filter->getField();
            $value = $filter->getValue();
            $fields[] = $field;
            $conditions[] = [$condition=>$value];
        }
        $collection->addFieldToFilter($fields, $conditions);
    }
}
