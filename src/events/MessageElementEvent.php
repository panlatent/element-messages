<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\event;

use craft\base\ElementInterface;
use craft\events\ModelEvent;
use panlatent\elementmessages\models\Message;

/**
 * Class MessageElementEvent
 *
 * @package panlatent\elementmessages\event
 * @property ElementInterface $sender
 * @author Panlatent <panlatent@gmail.com>
 */
class MessageElementEvent extends ModelEvent
{
    /**
     * @var Message|null
     */
    public $message;
}