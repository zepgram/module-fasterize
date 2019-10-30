<?php
/**
 * This file is part of Zepgram\Fasterize\ViewModel.
 *
 * @file       Cache.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\ViewModel;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zepgram\Fasterize\Model\Config;

/**
 * Class Cache
 * view model for additional template.
 */
class Cache implements ArgumentInterface
{
    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Cache constructor.
     *
     * @param FormKey                $formKey
     * @param StoreManagerInterface  $storeManagement
     * @param UrlInterface           $backendUrl
     * @param Config                 $config
     * @param AuthorizationInterface $authorization
     * @param LoggerInterface        $logger
     */
    public function __construct(
        FormKey $formKey,
        StoreManagerInterface $storeManagement,
        UrlInterface $backendUrl,
        Config $config,
        AuthorizationInterface $authorization,
        LoggerInterface $logger
    ) {
        $this->formKey = $formKey;
        $this->storeManager = $storeManagement;
        $this->backendUrl = $backendUrl;
        $this->config = $config;
        $this->authorization = $authorization;
        $this->logger = $logger;
    }

    /**
     * Get form key.
     *
     * @return null|string
     */
    public function getFormKey()
    {
        try {
            return $this->formKey->getFormKey();
        } catch (LocalizedException $e) {
            $this->logger->warning('Unable to generate formKey', $e->getTrace());
        }

        return null;
    }

    /**
     * Is config enabled for at least one store.
     *
     * @return bool
     */
    public function isBlockDisplayable()
    {
        $resource = $this->authorization->isAllowed('Zepgram_Fasterize::fasterize_cache_management');

        return \count($this->getStoreOptions()) > 0 && $resource;
    }

    /**
     * Get purge all url.
     *
     * @return string
     */
    public function getPurgeAllUrl()
    {
        return $this->backendUrl->getUrl('*/fasterize_purge/all');
    }

    /**
     * Get clean by source url.
     *
     * @return string
     */
    public function getCleanByStoreUrl()
    {
        return $this->backendUrl->getUrl('*/fasterize_purge/store');
    }

    /**
     * Get store options.
     *
     * @return array
     */
    public function getStoreOptions()
    {
        $storeOptions = [];
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            if ($this->config->isActive($store->getId())) {
                $storeOptions[] = $store;
            }
        }

        return $storeOptions;
    }
}
