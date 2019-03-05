<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\base;

use panlatent\elementmessages\models\Message;

/**
 * Interface SerializerInterface
 *
 * @package panlatent\elementmessages\base
 * @author Panlatent <panlatent@gmail.com>
 */
interface SerializerInterface
{
    /**
     * @param Message $message
     * @return string
     */
    public function serialize(Message $message): string;
}