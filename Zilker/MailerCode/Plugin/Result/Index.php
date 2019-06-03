<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Plugin\Result;

use Exception;
use Magento\CatalogSearch\Controller\Result\Index as ResultPageIndex;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Search\Model\QueryFactory;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;

/**
 * Class Index
 * @package Zilker\MailerCode\Controller\Result
 */
class Index
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var MailerCodeRepositoryInterface
     */
    private $codeRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FilterGroup
     */
    private $filterGroup;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * Index constructor.
     * @param QueryFactory $queryFactory
     * @param MailerCodeRepositoryInterface $codeRepository
     * @param FilterGroup $filterGroup
     * @param Filter $filter
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        QueryFactory $queryFactory,
        MailerCodeRepositoryInterface $codeRepository,
        FilterGroup $filterGroup,
        Filter $filter,
        SearchCriteriaInterface $searchCriteria
    ) {
        $this->queryFactory = $queryFactory;
        $this->codeRepository = $codeRepository;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @param ResultPageIndex $index
     * @param callable $proceed
     */
    public function aroundExecute(ResultPageIndex $index, callable $proceed) : void
    {
        $this->checkMailerCode($index);
//        $index->getResponse()->setNoCacheHeaders();
        $proceed();
    }

    /**
     * @param ResultPageIndex $index
     * @param string $key
     * @return mixed
     */
    protected function getSearchParams(ResultPageIndex $index, string $key)
    {
        return $index->getRequest()->getParam($key);
    }

    /**
     * @param ResultPageIndex $index
     */
    protected function checkMailerCode(ResultPageIndex $index)
    {
        $mailerCode = $this->getSearchParams($index, 'q');
        $this->searchMailerCode($mailerCode);
    }

    /**
     * @param string $mailerCode
     * @return array|mixed
     */
    protected function searchMailerCode(string $mailerCode)
    {
        $this->filter
            ->setField(MailerCodeInterface::SEARCH_CODE)
            ->setValue($mailerCode)
            ->setConditionType('eq');
        $this->filterGroup->setFilters([$this->filter]);
        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $mailerCodes = [];
        try {
            $mailerCodes = $this->codeRepository->getList($this->searchCriteria);
        } catch (Exception $exception) {
        }
        return $mailerCodes;
    }
}
