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
     * Purge by content type.
     *
     * @throws \Exception
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $result = false;
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

            if ($result) {
                $this->getMessageManager()->addSuccessMessage(__("The Fasterize cache has been cleaned for store: {$storeCodes}."));
            } else {
                $this->getMessageManager()->addErrorMessage(
                    __('The purge request was not processed successfully.')
                );
            }
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage(
                __('An error occurred while clearing the Fasterize Cache: ').$e->getMessage()
            );
        }

        return $this->_redirect('*/cache/index');
    }
}
