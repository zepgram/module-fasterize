<?php
/**
 * This file is part of Zepgram\Fasterize\Observer
 *
 * @package    Zepgram\Fasterize\Observer
 * @file       FlushAllCacheObserver.php
 * @date       13 09 2019 16:29
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
 * @license    MIT License
 */

namespace Zepgram\Fasterize\Observer;

use Zepgram\Fasterize\Http\PurgeRequest;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class FlushAllCacheObserver
 * auto-trigger cache clean
 */
class FlushAllCacheObserver implements ObserverInterface
{
    /**
     * @var PurgeRequest
     */
    private $purgeRequest;

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * FlushAllCacheObserver constructor.
     *
     * @param PurgeRequest     $purgeRequest
     * @param ManagerInterface $manager
     */
    public function __construct(
        PurgeRequest $purgeRequest,
        ManagerInterface $manager
    ) {
        $this->manager = $manager;
        $this->purgeRequest = $purgeRequest;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $results = $this->purgeRequest->flushAll();
        if ($results) {
            $this->manager->addSuccessMessage(__('Fasterize cache has been cleaned.'));
        }
    }
}
