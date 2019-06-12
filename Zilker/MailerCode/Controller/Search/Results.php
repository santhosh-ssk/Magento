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
use Magento\Framework\Controller\ResultInterface;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Psr\Log\LoggerInterface;
use Zilker\MailerCode\Helper\Data as HelperData;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Zilker\MailerCode\Model\Session as MailerCodeSession;

/**
 * Class Results
 * @package Zilker\MailerCode\Controller\Search
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var LoggerInterface $logger;
     */
    protected $logger;

    /**
     * @var ResultFactory $resultFactory
     */
    protected $resultFactory;

    /**
     * @var HttpContext $httpContext;
     */
    protected $httpContext;

    /**
     * @var MailerCodeSession $mailerCodeSession
     */
    protected $mailerCodeSession;

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
     * @param LoggerInterface $logger
     * @param ResultFactory $resultFactory
     * @param HttpContext $httpContext
     * @param MailerCodeSession $mailerCodeSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        LoggerInterface $logger,
        ResultFactory $resultFactory,
        HttpContext $httpContext,
        MailerCodeSession $mailerCodeSession
    ) {
        $this->helperData = $data;
        $this->queryFactory = $queryFactory;
        $this->mailerCodeRepository = $mailerCodeRepository;
        $this->productRepository = $productRepository;
        $this->filterFactory = $filterFactory;
        $this->filterGroup = $filterGroup;
        $this->searchCriteria = $searchCriteria;
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
        $this->httpContext = $httpContext;
        $this->mailerCodeSession = $mailerCodeSession;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

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
        $defaultContext = 0;
        $mailerCodeContext = $mailerCode ? 1 : $defaultContext;
        $this->httpContext->setValue('CONTEXT_MAILERCODE', $mailerCodeContext, $defaultContext);
        $this->mailerCodeSession->clearStorage();
        if ($mailerCode) {
            $productSku = $mailerCode->getSku();

            try {
                /** @var ProductInterfaceFactory $product */
                $product = $this->productRepository->get($productSku);
                $productUrl = $product->getProductUrl();
//                $productUrl = $this->helperData->getPdpRedirectUrl($productUrl, $mailerCode->getSearchCode());
                $this->mailerCodeSession->setMySession($mailerCode);
                $this->getResponse()->setNoCacheHeaders();
                $resultRedirect->setUrl($productUrl);
                $resultRedirect->setHttpResponseCode(307);
                return $resultRedirect;

            } catch (Exception $e) {
                $this->logger->info($e);
                $redirectUrl = $this->helperData->getCatalogSearchRedirectUrl($mailerSearchCode);
                $resultRedirect->setUrl($redirectUrl);
                return $resultRedirect;
            }

        } else {
            $redirectUrl = $this->helperData->getCatalogSearchRedirectUrl($mailerSearchCode);
            $resultRedirect->setUrl($redirectUrl);
            return $resultRedirect;
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
