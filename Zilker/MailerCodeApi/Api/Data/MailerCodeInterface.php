<?php
declare(strict_types=1);

namespace Zilker\MailerCodeApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface MailerCodeInterface
 * @package Zilker\MailerCodeApi\Api\Data
 */
interface MailerCodeInterface extends ExtensibleDataInterface
{
    const ENTITY_ID       = 'entity_id';
    const SKU             = 'sku';
    const MAILER_CODE     = 'mailer_code';
    const MIN_QUANTITY    = 'min_quantity';
    const EFFECTIVE_DATE  = 'effective_date';
    const EXPIRY_DATE     = 'expiry_date';
    const PRICE           = 'price';
    const SEARCH_CODE     = 'search_code';
    const SEARCH_ALT_CODE = 'search_alt_code';
    const IS_ACTIVE       = 'is_active';

    /**
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * @param string|null $sku
     * @return void
     */
    public function setSku(?string $sku): void;

    /**
     * @return string|null
     */
    public function getMailerCode(): ?string;

    /**
     * @param string|null $mailerCode
     * @return void
     */
    public function setMailerCode(?string $mailerCode): void;

    /**
     * @return int|null
     */
    public function getMinQuantity(): ?int;

    /**
     * @param int|null $minQuantity
     * @return void
     */
    public function setMinQuantity(?int $minQuantity): void;

    /**
     * @return string|null
     */
    public function getEffectiveDate(): ?string;

    /**
     * @param string|null $effectiveDate
     * @return void
     */
    public function setEffectiveDate(?string $effectiveDate): void;

    /**
     * @return string|null
     */
    public function getExpiryDate(): ?string;

    /**
     * @param string|null $expiryDate
     * @return void
     */
    public function setExpiryDate(?string $expiryDate): void;

    /**
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * @param float|null $price
     * @return void
     */
    public function setPrice(?float $price): void;

    /**
     * @return string|null
     */
    public function getSearchCode(): ?string;

    /**
     * @param string|null $searchCode
     * @return void
     */
    public function setSearchCode(?string $searchCode): void;

    /**
     * @return string|null
     */
    public function getSearchAltCode(): ?string;

    /**
     * @param string|null $searchAltCode
     * @return void
     */
    public function setSearchAltCode(?string $searchAltCode): void;

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool;

    /**
     * @param bool|null $isActive
     * @return void
     */
    public function setIsActive(?bool $isActive): void;
}
