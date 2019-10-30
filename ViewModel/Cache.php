<?php
/**
 * This file is part of Zepgram\Fasterize\ViewModel
 *
 * @package    Zepgram\Fasterize\ViewModel
 * @file       Cache.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Zepgram\Fasterize\Model\Config;
use Magento\Backend\Model\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Cache
 * view model for additional template
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Cache constructor.
     *
     * @param FormKey               $formKey
     * @param StoreManagerInterface $storeManagement
     * @param UrlInterface          $backendUrl
     * @param Config                $config
     * @param LoggerInterface       $logger
     */
    public function __construct(
        FormKey $formKey,
        StoreManagerInterface $storeManagement,
        UrlInterface $backendUrl,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->formKey = $formKey;
        $this->storeManager = $storeManagement;
        $this->backendUrl = $backendUrl;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Get form key.
     *
     * @return string|null
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
     * Is global config enabled.
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return count($this->getStoreOptions()) > 0;
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
