<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\events;

use panlatent\elementmessages\models\Message;
use yii\base\Event;

/**
 * Class MessageEvent
 *
 * @package panlatent\elementmessages\events
 * @author Panlatent <panlatent@gmail.com>
 */
class MessageEvent extends Event
{
    /**
     * @var Message|null
     */
    public $message;

    /**
     * @var bool
     */
    public $isNew = false;
}