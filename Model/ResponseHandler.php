<?php
/**
 * This file is part of Zepgram\Fasterize\Model
 *
 * @file       MessageManager.php
 * @date       30 10 2019 22:43
 *
 * @author     Benjamin Calef <zepgram@gmail.com>
 * @copyright  2019 Zepgram Copyright (c) (https://github.com/zepgram)
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
     * @var Config
     */
    private $config;

    /**
     * MessageManager constructor.
     *
     * @param ManagerInterface $messageManager
     * @param Config $config
     */
    public function __construct(
        ManagerInterface $messageManager,
        Config $config
    ) {
        $this->messageManager = $messageManager;
        $this->config = $config;
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

        foreach ($messages as $label => $message) {
            $label = \strtoupper($label);
            if (null === $message) {
                if (null === $successResponse) {
                    $successResponse = $label;

                    continue;
                }
                $successResponse .= ", ${label}";
            } else {
                if (null === $errorResponse) {
                    $errorResponse = "${label} ${message}";

                    continue;
                }
                $errorResponse .= ", ${label} ${message}";
            }
        }

        if ($level === 'addWarningMessage' && $this->config->isIgnoreWarning()) {
            $errorResponse = null;
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
