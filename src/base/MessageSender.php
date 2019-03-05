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
use panlatent\elementmessages\Plugin;

/**
 * Trait MessageSender
 *
 * @package panlatent\elementmessages\base
 * @author Panlatent <panlatent@gmail.com>
 */
trait MessageSender
{
    /**
     * Send a message.
     *
     * @param ElementInterface $target
     * @param ElementInterface $content
     * @return bool
     */
    public function send(ElementInterface $target, ElementInterface $content): bool
    {
        $message = new Message([
            'sender' => $this,
            'target' => $target,
            'content' => $content,
        ]);

        if (!$this->beforeSend($message)) {
            return false;
        }

        if (!Plugin::getInstance()->getMessages()->saveMessage($message)) {
            return false;
        }

        $this->afterSend($message);

        return true;
    }
}