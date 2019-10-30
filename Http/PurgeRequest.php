<?php
/**
 * This file is part of Zepgram\Fasterize\Http
 *
 * @package    Zepgram\Fasterize\Http
 * @file       PurgeRequest.php
 * @date       11 09 2019 17:49
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Http;

use Zepgram\Fasterize\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PurgeRequest
 * api request
 */
class PurgeRequest
{
    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RemoteApi constructor.
     *
     * @param CurlFactory           $curlFactory
     * @param SerializerInterface   $serializer
     * @param StoreManagerInterface $storeManager
     * @param Config                $config
     * @param LoggerInterface       $logger
     */
    public function __construct(
        CurlFactory $curlFactory,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @param int $storeId
     *
     * @throws LocalizedException
     *
     * @return bool|mixed
     */
    public function flush($storeId = 0)
    {
        return $this->sendRequest('cache', $storeId);
    }

    /**
     * @throws LocalizedException
     *
     * @return array|false
     */
    public function flushAll()
    {
        $results = false;
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $storeId = $store->getId();
            $storeCode = strtoupper($store->getCode());
            if ($this->config->isActive($storeId)) {
                $results[$storeCode] = $this->flush($storeId);
            }
        }

        return $results;
    }

    /**
     * Wrapper for API calls towards Fasterize service.
     *
     * @param string $service API Endpoint
     * @param int    $storeId API Auth
     * @param string $method  HTTP Method for request
     *
     * @throws LocalizedException
     *
     * @return bool|mixed
     */
    private function sendRequest(
        $service,
        $storeId = 0,
        $method = \Zend_Http_Client::DELETE
    ) {
        if (!$this->config->isActive($storeId)) {
            throw new LocalizedException(__('Fasterize is disabled in configuration'));
        }

        // Get config
        $uri = $this->config->getApiUrl($storeId).$this->config->getApiId($storeId)."/{$service}";
        $token = $this->config->getApiToken($storeId);

        // Client headers
        $headers = [
            "Authorization: {$token}",
            'Accept: application/json',
        ];

        // Client options
        $options[CURLOPT_CUSTOMREQUEST] = $method;

        /** @var Curl $client */
        $client = $this->curlFactory->create();

        // Execute request
        $client->setOptions($options);
        $client->write($method, $uri, '1.1', $headers);
        $response = $client->read();
        $client->close();

        // Parse response
        $responseBody = \Zend_Http_Response::extractBody($response);
        $responseCode = \Zend_Http_Response::extractCode($response);
        $responseCodeText = \Zend_Http_Response::responseCodeAsText($responseCode);
        $responseLog['method'] = [
            __METHOD__,
        ];
        $responseLog['request'] = [
            'store_id' => $storeId,
            'uri' => $uri,
            'http_method' => $method,
            'headers' => $headers,
        ];
        $responseLog['response'] = [
            'code' => $responseCode,
            'message' => $responseCodeText,
            'body' => $responseBody,
        ];

        $this->logger->info("Fasterize {$service} service", $responseLog);

        // Return error based on response code
        if (200 !== $responseCode) {
            $this->logger->error("Fasterize {$service} service", $responseLog);

            throw new LocalizedException(__($responseBody));
        }

        return $this->serializer->unserialize($responseBody);
    }
}
