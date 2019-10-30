<?php
/**
 * This file is part of Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge.
 *
 * @file       Store.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zepgram\Fasterize\Http\PurgeRequest;

/**
 * Class Store
 * purge by store.
 */
class Store extends Action
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PurgeRequest
     */
    private $purgeRequest;

    /**
     * Store constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param PurgeRequest          $purgeRequest
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PurgeRequest $purgeRequest
    ) {
        $this->storeManager = $storeManager;
        $this->purgeRequest = $purgeRequest;
        parent::__construct($context);
    }

    /**
     * Purge by store.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        try {
            $storeId = $this->getRequest()->getParam('stores', false);
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore($storeId);
            $storeCode = \strtoupper($store->getCode());
            $this->purgeRequest->flush($storeId);

            $this->getMessageManager()
                ->addSuccessMessage(__("The Fasterize cache has been cleaned for store: {$storeCode}."))
            ;
        } catch (Exception $e) {
            $this->getMessageManager()
                ->addErrorMessage(__('An error occurred while clearing the Fasterize Cache: %1', $e->getMessage()))
            ;
        }

        return $this->_redirect('*/cache/index');
    }
}
