<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\base;

use craft\base\ElementInterface;
use panlatent\elementmessages\models\Message;

/**
 * Interface MessageSenderInterface
 *
 * @package panlatent\elementmessages\base
 * @author Panlatent <panlatent@gmail.com>
 */
interface MessageSenderInterface
{
    /**
     * Send a message.
     *
     * @param ElementInterface $target
     * @param ElementInterface $content
     * @return bool
     */
    public function send(ElementInterface $target, ElementInterface $content): bool;

    /**
     * Before send message.
     *
     * @param Message $message
     * @return bool
     */
    public function beforeSend(Message $message): bool;

    /**
     * After send message.
     *
     * @param Message $message
     */
    public function afterSend(Message $message);
}