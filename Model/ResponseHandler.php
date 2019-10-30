<?php
/**
 * This file is part of Zepgram\Fasterize\Model for Caudalie.
 *
 * @file       MessageManager.php
 * @date       30 10 2019 22:43
 *
 * @author     bcalef <benjamin.calef@caudalie.com>
 * @copyright  2019 Caudalie Copyright (c) (https://caudalie.com)
 * @license    proprietary
 */

namespace Zepgram\Fasterize\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;

class ResponseHandler
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * MessageManager constructor.
     *
     * @param ManagerInterface $messageManager
     */
    public function __construct(ManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param DataObject $result
     * @param string     $level
     */
    public function manageResult(DataObject $result, $level = 'addErrorMessage')
    {
        $successResponse = null;
        $errorResponse = null;
        $messages = $result->getData();
        if (null === $messages) {
            return;
        }

        foreach ($messages as $key => $message) {
            $key = \strtoupper($key);
            if (null === $message) {
                if (null === $successResponse) {
                    $successResponse = $key;

                    continue;
                }
                $successResponse .= ", ${key}";
            } else {
                if (null === $errorResponse) {
                    $errorResponse = "${key} ${message}";

                    continue;
                }
                $errorResponse .= ", ${key} ${message}";
            }
        }

        if (null !== $errorResponse) {
            $this->messageManager->{$level}(
                __("An error occurred while clearing the Fasterize cache: {$errorResponse}.")
            );
        }

        if (null !== $successResponse) {
            $this->messageManager->addSuccessMessage(
                __("The Fasterize cache has been cleaned for store view: {$successResponse}.")
            );
        }
    }
}
