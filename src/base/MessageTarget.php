<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\base;

use panlatent\elementmessages\models\Message;
use panlatent\elementmessages\Plugin;

/**
 * Trait MessageTarget
 *
 * @package panlatent\elementmessages\base
 * @property-read int $totalMessages
 * @property-read Message|null $lastMessage
 * @author Panlatent <panlatent@gmail.com>
 */
trait MessageTarget
{
    /**
     * @var int|null
     */
    private $_totalMessages;

    /**
     * @var Message|null
     */
    private $_lastMessage;

    /**
     * @inheritdoc
     */
    public function getTotalMessages(): int
    {
        if ($this->_totalMessages !== null) {
            return $this->_totalMessages;
        }

        if (!$this->id) {
            return 0;
        }

        $this->_totalMessages = Plugin::getInstance()
            ->getMessages()
            ->getTotalMessages([
                'targetId' => $this->id
            ]);

        return $this->_totalMessages;
    }

    /**
     * @inheritdoc
     */
    public function getLastMessage()
    {
        if ($this->_lastMessage !== null) {
            return $this->_lastMessage;
        }

        if (!$this->id) {
            return null;
        }

        $this->_lastMessage = Plugin::getInstance()->getMessages()
            ->findMessage([
                'targetId' => $this->id,
                'order' => 'postDate DESC, id DESC'
            ]);

        return $this->_lastMessage;
    }

    /**
     * @inheritdoc
     */
    public function isAcceptableMessage(Message $message)
    {
        if ($message->getTarget() !== $this) {
            return false;
        }

        return true;
    }
}