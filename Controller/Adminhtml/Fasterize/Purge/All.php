<?php
/**
 * This file is part of Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge.
 *
 * @file       All.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Controller\Adminhtml\Fasterize\Purge;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Zepgram\Fasterize\Http\PurgeRequest;
use Zepgram\Fasterize\Model\ResponseHandler;

/**
 * Class All
 * purge globally.
 */
class All extends Action
{
    /**
     * @var string
     */
    const ADMIN_RESOURCE = 'Zepgram_Fasterize::fasterize_cache_management';

    /**
     * @var PurgeRequest
     */
    private $purgeRequest;

    /**
     * @var ResponseHandler
     */
    private $responseHandler;

    /**
     * Store constructor.
     *
     * @param Context         $context
     * @param PurgeRequest    $purgeRequest
     * @param ResponseHandler $responseHandler
     */
    public function __construct(
        Context $context,
        PurgeRequest $purgeRequest,
        ResponseHandler $responseHandler
    ) {
        $this->purgeRequest = $purgeRequest;
        $this->responseHandler = $responseHandler;
        parent::__construct($context);
    }

    /**
     * Purge all.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $result = $this->purgeRequest->flushAll();
        $this->responseHandler->manageResult($result);

        return $this->_redirect('*/cache/index');
    }
}
