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
 * Interface MessageParserInterface
 *
 * @package panlatent\elementmessages\base
 * @author Panlatent <panlatent@gmail.com>
 */
interface ParserInterface
{
    /**
     * @param mixed $config
     * @return Message|null
     */
    public function parse($config);
}