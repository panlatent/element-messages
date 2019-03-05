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

}