<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Controller\Search;

use Exception;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Psr\Log\LoggerInterface;
use Zilker\MailerCode\Helper\Data as HelperData;
use Zilker\MailerCode\Model\Session;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;

/**
 * Class Results
 * @package Zilker\MailerCode\Controller\Search
 */
class Results extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var HelperData $helperData
     */
    protected $helperData;

    /**
     * @var QueryFactory $queryFactory
     */
    protected $queryFactory;

    /**
     * @var MailerCodeRepositoryInterface $mailerCodeRepository
     */
    protected $mailerCodeRepository;

    /**
     * @var ProductRepositoryInterface $productRepository
     */
    protected $productRepository;

    /**
     * @var FilterFactory $filter
     */
    protected $filterFactory;

    /**
     * @var FilterGroup $filterGroup
     */
    protected $filterGroup;

    /**
     * @var SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var Session $mailerCodeSession
     */
    protected $mailerCodeSession;

    /**
     * @var LoggerInterface $logger;
     */
    protected $logger;

    protected $httpContext;

    /**
     * Results constructor.
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param HelperData $data
     * @param MailerCodeRepositoryInterface $mailerCodeRepository
     * @param ProductRepositoryInterface $productRepository
     * @param FilterFactory $filterFactory
     * @param FilterGroup $filterGroup
     * @param SearchCriteriaInterface $searchCriteria
     * @param Session $session
     */
    public function __construct(
        Context $context,
        QueryFactory $queryFactory,
        HelperData $data,
        MailerCodeRepositoryInterface $mailerCodeRepository,
        ProductRepositoryInterface $productRepository,
        FilterFactory $filterFactory,
        FilterGroup $filterGroup,
        SearchCriteriaInterface $searchCriteria,
        Session $session,
        LoggerInterface $logger,
        HttpContext $httpcontext
    ) {
        $this->helperData = $data;
        $this->queryFactory = $queryFactory;
        $this->mailerCodeRepository = $mailerCodeRepository;
        $this->productRepository = $productRepository;
        $this->filterFactory = $filterFactory;
        $this->filterGroup = $filterGroup;
        $this->searchCriteria = $searchCriteria;
        $this->mailerCodeSession = $session;
        $this->logger = $logger;
        $this->httpContext = $httpcontext;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        /** @var $query Query */
        $query = $this->queryFactory->get();
        $mailerSearchCode = $query->getQueryText();
        /** @var array $mailerCodes */
        $mailerCodes = $this->fetchMailerCode($mailerSearchCode);
        /** @var MailerCodeInterface $mailerCode */
        $mailerCode = null;
        if (count($mailerCodes) > 0) {
            $mailerCode = $mailerCodes[0];
        }

        $defaultMailerContext = 0;
        $mailerCodeContext = $mailerCodes ? $mailerCode->getEntityId() : $defaultMailerContext;
        $this->httpContext->setValue('CONTEXT_MAILER_CODE', $mailerCodeContext, $defaultMailerContext);
        $this->logger->info("mailerCode Context: " . json_encode($mailerCodeContext));
        $this->mailerCodeSession->clearStorage();

        if ($mailerCode) {
            $productSku = $mailerCode->getSku();
            try {
                /** @var ProductInterfaceFactory $product */
                $product = $this->productRepository->get($productSku);
                $productUrl = $product->getProductUrl();
                $this->logger->info("product uri: " . $productUrl);

                $this->mailerCodeSession->setMySession($mailerCode);
                $this->getResponse()->setRedirect($productUrl);
            } catch (\Exception $e) {
                $this->logger->info($e);
                $redirectUrl = $this->helperData->getRedirectUrl($mailerSearchCode);
                $this->getResponse()->setRedirect($redirectUrl);
            }
        } else {
            $redirectUrl = $this->helperData->getRedirectUrl($mailerSearchCode);
            $this->getResponse()->setRedirect($redirectUrl);
        }
    }

    /**
     * @param String $mailerCode
     * @return array|mixed
     */
    public function fetchMailerCode(String $mailerCode)
    {

        /** @var Filter $filter1 */
        $filter1 = $this->filterFactory->create();
        /** @var Filter $filter2 */
        $filter2 = $this->filterFactory->create();

        $filter1
            ->setField(MailerCodeInterface::SEARCH_CODE)
            ->setValue($mailerCode)
            ->setConditionType('eq');

        $filter2
            ->setField(MailerCodeInterface::SEARCH_ALT_CODE)
            ->setValue($mailerCode)
            ->setConditionType('eq');

        $this->filterGroup->setFilters([$filter1, $filter2]);
        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $mailerCodes = [];
        try {
            $mailerCodes = $this->mailerCodeRepository->getList($this->searchCriteria);
        } catch (Exception $exception) {
            $this->logger->info('Error: ' . $exception);
        }
        return $mailerCodes;
    }
}
