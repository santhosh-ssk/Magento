<?php

namespace Zilker\MailerCodeImport\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Helper\Data as ImportExportData;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Zilker\MailerCodeApi\Api\MailerCodeRepositoryInterface;
use Zilker\MailerCodeImport\Model\Import\Validator\RowValidatorInterface as ValidatorInterface;

/**
 * Class MailerCodeImport
 * @package Zilker\MailerCodeImport\Model\Import
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class MailerCodeImport extends AbstractEntity
{
    const TABLE_ENTITY    = 'zilker_product_mailer_code';
    const ENTITY_ID       = 'entity_id';
    const SKU             = 'sku';
    const MAILER_CODE     = 'mailer_code';
    const MIN_QUANTITY    = 'min_quantity';
    const EFFECTIVE_DATE  = 'effective_date';
    const EXPIRY_DATE     = 'expiry_date';
    const PRICE           = 'price';
    const IS_ACTIVE       = 'is_active';
    const SEARCH_CODE     = 'search_code';
    const SEARCH_ALT_CODE = 'search_alt_code';

    protected $_permanentAttributes = [self::ENTITY_ID];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::ENTITY_ID,
        self::SKU,
        self::MAILER_CODE,
        self::MIN_QUANTITY,
        self::EFFECTIVE_DATE,
        self::EXPIRY_DATE,
        self::PRICE,
    ];
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * Import data rows
     *
     * @return boolean
     */

    /**
     * @var DateTime
     */
    protected $_connection;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var MailerCodeRepositoryInterface
     */
    protected $codeRepository;

    /**
     * MailerCodeImport constructor.
     * @param Data $jsonHelper
     * @param ImportExportData $importExportData
     * @param ImportData $importData
     * @param ResourceConnection $resource
     * @param ResourceHelper $resourceHelper
     * @param StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param MailerCodeRepositoryInterface $codeRepository
     */
    public function __construct(
        Data $jsonHelper,
        ImportExportData $importExportData,
        ImportData $importData,
        ResourceConnection $resource,
        ResourceHelper $resourceHelper,
        StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        MailerCodeRepositoryInterface $codeRepository
    ) {
        $this->jsonHelper        = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_dataSourceModel  = $importData;
        $this->_resource         = $resource;
        $this->_resourceHelper   = $resourceHelper;
        $this->_connection       = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator   = $errorAggregator;
        $this->codeRepository    = $codeRepository;
        $this->string            = $string;
    }
    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'zilker_product_mailer_code';
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        if (Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveOrReplaceEntity();
        } elseif (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
        } elseif (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
        }
        return true;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
        }
        $this->_validatedRows[$rowNumber] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
    }

    /**
     *
     */
    public function saveOrReplaceEntity()
    {
        $behaviour = $this->getBehavior();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowId = $rowData[self::ENTITY_ID];
                $mailerCodeIds[] = $rowId;
                $entityList[$rowId][] = [
                    self::MAILER_CODE     => $rowData[self::MAILER_CODE],
                    self::SKU             => $rowData[self::SKU],
                    self::MIN_QUANTITY    => $rowData[self::MIN_QUANTITY],
                    self::EFFECTIVE_DATE  => $rowData[self::EFFECTIVE_DATE],
                    self::EXPIRY_DATE     => $rowData[self::EXPIRY_DATE],
                    self::PRICE           => $rowData[self::PRICE],
                    self::SEARCH_CODE     => $rowData[self::MAILER_CODE] . $rowData[self::SKU],
                    self::SEARCH_ALT_CODE => $rowData[self::MAILER_CODE] . ' ' . $rowData[self::SKU],
                ];
            }
            if (Import::BEHAVIOR_REPLACE == $behaviour) {
                if ($mailerCodeIds) {
                }
            } elseif (Import::BEHAVIOR_APPEND == $behaviour) {
                $this->saveEntity($entityList, self::TABLE_ENTITY);
            }
        }
    }

    /**        $mailerCodes = [];

     * @param $entityList
     * @param $table
     * @return MailerCodeImport
     */
    protected function saveEntity($entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRow) {
                foreach ($entityRow as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn, [
                   self::MAILER_CODE,
                   self::SKU,
                   self::MIN_QUANTITY,
                   self::EFFECTIVE_DATE,
                   self::EXPIRY_DATE,
                   self::PRICE,
                   self::SEARCH_CODE,
                   self::SEARCH_ALT_CODE
                ]);
            }
        }
        return $this;
    }
}
