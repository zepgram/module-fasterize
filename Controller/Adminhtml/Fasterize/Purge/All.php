<?php
/**
 * This file is part of Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge
 *
 * @package    Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge
 * @file       All.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Zepgram\Fasterize\Http\PurgeRequest;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class All
 * purge globally
 */
class All extends Action
{
    /**
     * @var PurgeRequest
     */
    private $purgeRequest;

    /**
     * Store constructor.
     *
     * @param Context      $context
     * @param PurgeRequest $purgeRequest
     */
    public function __construct(
        Context $context,
        PurgeRequest $purgeRequest
    ) {
        $this->purgeRequest = $purgeRequest;
        parent::__construct($context);
    }

    /**
     * Purge all
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $storeCodes = null;
        try {
            $results = $this->purgeRequest->flushAll();
            if ($results) {
                foreach ($results as $storeCode => $result) {
                    if (!$storeCodes) {
                        $storeCodes .= $storeCode;
                    } else {
                        $storeCodes .= ", {$storeCode}";
                    }
                }
            }

            $this->getMessageManager()
                ->addSuccessMessage(__("The Fasterize cache has been cleaned for store: {$storeCodes}."));
        } catch (Exception $e) {
            $this->getMessageManager()
                ->addErrorMessage(__('An error occurred while clearing the Fasterize Cache: %1', $e->getMessage()));
        }

        return $this->_redirect('*/cache/index');
    }
}
