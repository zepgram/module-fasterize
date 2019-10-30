<?php
/**
 * This file is part of Zepgram\Fasterize\Exception for Caudalie.
 *
 * @file       FasterizeException.php
 * @date       30 10 2019 20:57
 *
 * @author     bcalef <benjamin.calef@caudalie.com>
 * @copyright  2019 Caudalie Copyright (c) (https://caudalie.com)
 * @license    proprietary
 */

namespace Zepgram\Fasterize\Exception;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class FasterizeException extends LocalizedException
{
    /**
     * Exception thrown while processing Fasterize cache flush service.
     *
     * @param Phrase    $phrase
     * @param Exception $cause
     * @param int       $code
     */
    public function __construct(Phrase $phrase = null, Exception $cause = null, $code = 0)
    {
        if (null === $phrase) {
            $phrase = new Phrase(__('An unknown exception occurred with Fasterize.'));
        }
        parent::__construct($phrase, $cause, $code);
    }
}
