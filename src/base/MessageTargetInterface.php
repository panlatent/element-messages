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
 * Interface MessageTargetInterface
 *
 * @package panlatent\elementmessages\base
 * @author Panlatent <panlatent@gmail.com>
 */
interface MessageTargetInterface
{
    /**
     * Returns the total number of received messages.
     *
     * @return int
     */
    public function getTotalMessages(): int;

    /**
     * Returns last received message.
     *
     * @return Message|null
     */
    public function getLastMessage();

    /**
     * Return received messages with criteria.
     *
     * @param mixed $criteria
     * @return Message[]
     */
    public function getMessages($criteria = null);

    /**
     * Receive a message.
     *
     * @param Message $message
     * @return bool
     */
    public function isAcceptableMessage(Message $message): bool;

    /**
     * Accept a message.
     *
     * @param Message $message
     */
    public function acceptMessage(Message $message);
}