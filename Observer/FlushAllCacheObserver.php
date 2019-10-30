<?php
/**
 * This file is part of Zepgram\Fasterize\Observer.
 *
 * @file       FlushAllCacheObserver.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zepgram\Fasterize\Http\PurgeRequest;
use Zepgram\Fasterize\Model\ResponseHandler;

/**
 * Class FlushAllCacheObserver
 * auto-trigger cache clean.
 */
class FlushAllCacheObserver implements ObserverInterface
{
    /**
     * @var PurgeRequest
     */
    private $purgeRequest;

    /**
     * @var ResponseHandler
     */
    private $responseHandler;

    /**
     * @var bool
     */
    private $purgeFlag;

    /**
     * FlushAllCacheObserver constructor.
     *
     * @param PurgeRequest    $purgeRequest
     * @param ResponseHandler $responseHandler
     */
    public function __construct(
        PurgeRequest $purgeRequest,
        ResponseHandler $responseHandler
    ) {
        $this->purgeRequest = $purgeRequest;
        $this->responseHandler = $responseHandler;
    }

    /**
     * Flush all event
     * actions here are not explicitly requested so error will be printed as warning
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (null === $this->purgeFlag) {
            $result = $this->purgeRequest->flushAll();
            $this->purgeFlag = true;
            $this->responseHandler->manageResult($result, 'addWarningMessage');
        }
    }
}
