<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax16
 * @copyright Copyright (c) 2016 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax16;

/**
 * Class \OnePica\AvaTax16\Config
 */
class Config
{
    /**
     * Accept header
     */
    const ACCEPT_HEADER = 'application/json; document-version=1';

    /**
     * Content type header
     */
    const CONTENT_TYPE_HEADER = 'application/json';

    /**
     * Default user agent
     */
    const USER_AGENT_DEFAULT = 'AvaTax16 agent';

    /**
     * Base url
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Account id
     *
     * @var string
     */
    protected $accountId;

    /**
     * Company code
     *
     * @var string
     */
    protected $companyCode;

    /**
     * Authorization header
     *
     * @var string
     */
    protected $authorizationHeader;

    /**
     * User agent
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Construct
     */
    public function __construct()
    {
        // init default values
        $this->setUserAgent(self::USER_AGENT_DEFAULT);
    }

    /**
     * Set base url
     *
     * @param string $value
     * @return \OnePica\AvaTax16\Config
     */
    public function setBaseUrl($value)
    {
        $this->baseUrl = $value;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set user agent
     *
     * @param string $value
     * @return \OnePica\AvaTax16\Config
     */
    public function setUserAgent($value)
    {
        $this->userAgent = $value;
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set account id
     *
     * @param string $value
     * @return \OnePica\AvaTax16\Config
     */
    public function setAccountId($value)
    {
        $this->accountId = $value;
    }

    /**
     * Get account id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set company code
     *
     * @param string $value
     * @return \OnePica\AvaTax16\Config
     */
    public function setCompanyCode($value)
    {
        $this->companyCode = $value;
    }

    /**
     * Get company code
     *
     * @return string
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * Set authorization header
     *
     * @param string $value
     * @return \OnePica\AvaTax16\Config
     */
    public function setAuthorizationHeader($value)
    {
        $this->authorizationHeader = $value;
    }

    /**
     * Get authorization header
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        return $this->authorizationHeader;
    }

    /**
     * Get accept header
     *
     * @return string
     */
    public function getAcceptHeader()
    {
        return self::ACCEPT_HEADER;
    }

    /**
     * Get accept header
     *
     * @return string
     */
    public function getContentTypeHeader()
    {
        return self::CONTENT_TYPE_HEADER;
    }

    /**
     * Get if config values are available for requests
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->getBaseUrl()
            && $this->getAccountId()
            && $this->getCompanyCode()
            && $this->getAuthorizationHeader()
            && $this->getAcceptHeader()
            && $this->getContentTypeHeader()
            && $this->getUserAgent()
        ) {
            return true;
        } else {
            return false;
        }
    }
}
