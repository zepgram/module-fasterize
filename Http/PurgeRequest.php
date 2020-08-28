<?php
/**
 * This file is part of Zepgram\Fasterize\Http
 *
 * @file       PurgeRequest.php
 * @date       11 09 2019 17:49
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Http;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zepgram\Fasterize\Exception\FasterizeException;
use Zepgram\Fasterize\Model\Config;

/**
 * Class PurgeRequest
 * api request.
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
     * @var DataObject
     */
    private $response;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

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
     * @param DataObjectFactory     $dataObjectFactory
     * @param LoggerInterface       $logger
     */
    public function __construct(
        CurlFactory $curlFactory,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        Config $config,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger
    ) {
        $this->curlFactory = $curlFactory;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * @param int $storeId
     *
     * @return null|DataObject
     */
    public function flush($storeId = 0)
    {
        if (null === $this->response) {
            $this->response = $this->dataObjectFactory->create();
        }

        try {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();

            try {
                $this->sendRequest('cache', $storeId);
                $this->response->setData($storeCode);
            } catch (FasterizeException $e) {
                $this->response->setData($storeCode, $e->getMessage());
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->response;
    }

    /**
     * @return null|DataObject
     */
    public function flushAll()
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();
            if ($this->config->isActive($storeId)) {
                $this->flush($storeId);
            }
        }

        return $this->response;
    }

    /**
     * Wrapper for API calls towards Fasterize service.
     *
     * @param string $service API Endpoint
     * @param int    $storeId API Auth
     * @param string $method  HTTP Method for request
     *
     * @throws FasterizeException
     *
     * @return bool|mixed
     */
    private function sendRequest(
        $service,
        $storeId = 0,
        $method = \Zend_Http_Client::DELETE
    ) {
        // Validate config
        if (!$this->config->isActive($storeId)) {
            throw new FasterizeException(__('Fasterize is disabled in configuration'));
        }
        $apiUrl = $this->config->getApiUrl($storeId);
        if (!$apiUrl) {
            throw new FasterizeException(__('API url is not configured'));
        }
        $apiToken = $this->config->getApiToken($storeId);
        if (!$apiToken) {
            throw new FasterizeException(__('API token is not configured'));
        }
        $apiId = $this->config->getApiId($storeId);
        if (!$apiId) {
            throw new FasterizeException(__('API id is not configured'));
        }
        $uri = $apiUrl . $apiId . "/{$service}";

        // Client headers
        $headers = [
            "Authorization: {$apiToken}",
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

        // Return error based on response code
        if (200 !== (int) $responseCode) {
            $this->logger->error("Request $method for service {$service}", $responseLog);

            throw new FasterizeException(__("${responseCode} - ${responseCodeText}"));
        }
        $this->logger->info("Request $method for service {$service}", $responseLog);

        return $this->serializer->unserialize($responseBody);
    }
}
