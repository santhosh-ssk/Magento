<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Model;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Zilker\MailerCode\Api\FetchMailerCodeInterface;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;

/**
 * Class FetchMailerCode
 * @package Zilker\MailerCode\Model
 */
class FetchMailerCode implements FetchMailerCodeInterface
{
    /**
     * @var Filter $filter
     */
    protected $filter;

    /**
     * @var FilterGroup $filterGroup
     */
    protected $filterGroup;

    /**
     * @var SearchCriteriaInterface $searchCriteria
     */
    protected $searchCriteria;

    /**
     * @var MailerCodeRepositoryInterface $mailerCodeRepository
     */
    protected $mailerCodeRepository;

    /**
     * FetchMailerCode constructor.
     * @param Filter $filter
     * @param FilterGroup $filterGroup
     * @param SearchCriteriaInterface $searchCriteria
     * @param MailerCodeRepositoryInterface $codeRepository
     */
    public function __construct(
        Filter $filter,
        FilterGroup $filterGroup,
        SearchCriteriaInterface $searchCriteria,
        MailerCodeRepositoryInterface $codeRepository
    ) {
        $this->filter = $filter;
        $this->filterGroup = $filterGroup;
        $this->searchCriteria = $searchCriteria;
        $this->mailerCodeRepository = $codeRepository;
    }

    /**
     * @param string $searchCode
     * @return MailerCodeInterface|null
     */
    public function getBySearchCode(string $searchCode): ?MailerCodeInterface
    {
        $this->filter
            ->setField(MailerCodeInterface::SEARCH_CODE)
            ->setValue($searchCode)
            ->setConditionType('eq');

        $this->filterGroup->setFilters([$this->filter]);
        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $mailerCodes = $this->mailerCodeRepository->getList($this->searchCriteria);
        /**
         * @var MailerCodeInterface $mailerCode
         */
        $mailerCode = null;
        if (count($mailerCodes)>0) {
            $mailerCode = $mailerCodes[0];
        }
        return $mailerCode;
    }
}
