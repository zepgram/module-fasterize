<?php
/**
 * This file is part of Zepgram\Fasterize\Model.
 *
 * @file       Config.php
 * @date       11 09 2019 17:38
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * scope config data.
 */
class Config
{
    const XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_ACTIVE = 'zepgram_fasterize/general/active';
    const XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_URL = 'zepgram_fasterize/general/api_url';
    const XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_ID = 'zepgram_fasterize/general/api_id';
    const XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_TOKEN = 'zepgram_fasterize/general/api_token';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface   $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function isActive($storeId = 0)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getApiUrl($storeId = 0)
    {
        $apiUrl = (string) $this->scopeConfig->getValue(
            self::XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!empty($apiUrl) && DIRECTORY_SEPARATOR !== \substr($apiUrl, -1)) {
            $apiUrl .= DIRECTORY_SEPARATOR;
        }

        return $apiUrl;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getApiId($storeId = 0)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getApiToken($storeId = 0)
    {
        $token = (string) $this->scopeConfig->getValue(
            self::XML_PATH_ZEPGRAM_FASTERIZE_GENERAL_API_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->encryptor->decrypt($token);
    }
}
