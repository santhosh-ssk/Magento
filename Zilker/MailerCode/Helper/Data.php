<?php
declare(strict_types=1);

namespace Zilker\MailerCode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Search\Model\QueryFactory;

/**
 * Class Data
 * @package Zilker\MailerCode\Helper
 */
class Data extends AbstractHelper
{
    const MAILERCODE = 'mailercode';

    /**
     * Retrieve result page url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @param   string $query
     * @return  string
     */
    public function getResultUrl($query = null)
    {
        return $this->_getUrl(
            'mailercode/search/results',
            ['_query' => [QueryFactory::QUERY_VAR_NAME => $query], '_secure' => $this->_request->isSecure()]
        );
    }

    /**
     * Retrieve search page url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @param   string $query
     * @return  string
     */
    public function getCatalogSearchRedirectUrl($query = null)
    {
        return $this->_getUrl(
            'catalogsearch/result',
            ['_query' => [QueryFactory::QUERY_VAR_NAME => $query], '_secure' => $this->_request->isSecure()]
        );
    }

    /**
     * Retrieve search page url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @param String $url
     * @param string $mailerCode
     * @return  string
     */
    public function getPdpRedirectUrl(String $url, String $mailerCode)
    {
        return $url . '?mailercode= ' . $mailerCode;
//        return $this->_getUrl(
//            $url,
//            ['_query' => [$this::MAILERCODE => $mailerCode], '_secure' => $this->_request->isSecure()]
//        );
    }
}
