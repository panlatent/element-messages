<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\elements;

use craft\base\Element;
use panlatent\elementmessages\base\MessageSender;
use panlatent\elementmessages\base\MessageSenderInterface;
use panlatent\elementmessages\base\MessageTarget;
use panlatent\elementmessages\base\MessageTargetInterface;
use panlatent\elementmessages\event\MessageElementEvent;
use panlatent\elementmessages\events\MessageEvent;
use panlatent\elementmessages\models\Message;

/**
 * Class MessageElement
 *
 * @package panlatent\elementmessages\elements
 * @author Panlatent <panlatent@gmail.com>
 */
abstract class MessageElement extends Element implements MessageSenderInterface, MessageTargetInterface
{
    // Traits
    // =========================================================================

    use MessageSender, MessageTarget;

    // Constants
    // =========================================================================

    /**
     * @event
     */
    const EVENT_BEFORE_MESSAGE_SEND = 'beforeMessageSend';

    /**
     * @event
     */
    const EVENT_AFTER_MESSAGE_SEND = 'afterMessageSend';

    /**
     * @event
     */
    const EVENT_MESSAGE_ARRIVED = 'messageArrived';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function acceptMessage(Message $message)
    {
        if ($this->hasEventHandlers(self::EVENT_MESSAGE_ARRIVED)) {
            $this->trigger(self::EVENT_MESSAGE_ARRIVED, new MessageEvent([
                'message' => $this,
                'isNew' => false,
            ]));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSend(Message $message): bool
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_MESSAGE_SEND)) {
            $event = new MessageElementEvent([
                'message' => $message,
            ]);

            $this->trigger(self::EVENT_BEFORE_MESSAGE_SEND, $event);

            return $event->isValid;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSend(Message $message)
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_MESSAGE_SEND)) {
            $this->trigger(self::EVENT_AFTER_MESSAGE_SEND, new MessageElementEvent([
                'message' => $message,
            ]));
        }
    }
}