<?php
declare(strict_types=1);
namespace Zilker\MailerCodeApi\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Zilker\MailerCodeApi\Api\Data\MailerCodeInterface;
use Zilker\MailerCodeApi\Model\ResourceModel\MailerCode as MailerCodeResourceModel;

/**
 * Class MailerCode
 * @package Zilker\MailerCode\Model
 */
class MailerCode extends AbstractExtensibleModel implements MailerCodeInterface
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(MailerCodeResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU) === null ?
        null : (string)$this->getData(self::SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku(?string $sku): void
    {
        $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getMailerCode(): ?string
    {
        return $this->getData(self::MAILER_CODE) === null ?
        null : (string)$this->getData(self::MAILER_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setMailerCode(?string $mailerCode): void
    {
        $this->setData(self::MAILER_CODE, $mailerCode);
    }

    /**
     * @inheritdoc
     */
    public function getMinQuantity(): ?int
    {
        return $this->getData(self::MIN_QUANTITY) == null ?
        null : (int)$this->getData(self::MIN_QUANTITY);
    }

    /**
     * @inheritdoc
     */
    public function setMinQuantity(?int $minQuantity): void
    {
        $this->setData(self::MIN_QUANTITY, $minQuantity);
    }

    /**
     * @inheritdoc
     */
    public function getEffectiveDate(): ?string
    {
        return $this->getData(self::EFFECTIVE_DATE) === null ?
        null : (string)$this->getData(self::EFFECTIVE_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setEffectiveDate(?string $effectiveDate): void
    {
        $this->setData(self::EFFECTIVE_DATE, $effectiveDate);
    }

    /**
     * @inheritdoc
     */
    public function getExpiryDate(): ?string
    {
        return $this->getData(self::EXPIRY_DATE) === null ?
        null : (string)$this->getData(self::EXPIRY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setExpiryDate(?string $expiryDate): void
    {
        $this->setData(self::EXPIRY_DATE, $expiryDate);
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): ?float
    {
        return $this->getData(self::PRICE) === null ?
        null : (float)$this->getData(self::PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setPrice(?float $price): void
    {
        $this->setData(self::SEARCH_CODE, $price);
    }

    /**
     * @inheritdoc
     */
    public function getSearchCode(): ?string
    {
        return $this->getData(self::SEARCH_CODE) === null ?
        null : (string)$this->getData(self::SEARCH_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setSearchCode(?string $searchCode): void
    {
        $this->setData(self::SEARCH_CODE, $searchCode);
    }

    /**
     * @inheritdoc
     */
    public function getSearchAltCode(): ?string
    {
        return $this->getData(self::SEARCH_ALT_CODE) === null ?
        null : (string)$this->getData(self::SEARCH_ALT_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setSearchAltCode(?string $searchAltCode): void
    {
        $this->setData(self::SEARCH_ALT_CODE, $searchAltCode);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): ?bool
    {
        return $this->getData(self::IS_ACTIVE) === null ?
        null : (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * @param bool|null $isActive
     */
    public function setIsActive(?bool $isActive): void
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }
}
