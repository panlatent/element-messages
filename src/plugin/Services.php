<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\plugin;

use panlatent\elementmessages\services\Messages;

/**
 * Trait Services
 *
 * @package panlatent\elementmessages\plugin
 * @property-read Messages $messages
 * @author Panlatent <panlatent@gmail.com>
 */
trait Services
{
    /**
     * @return Messages
     */
    public function getMessages(): Messages
    {
        return $this->get('messages');
    }

    /**
     * Set plugin services.
     */
    private function _setComponents()
    {
        $this->setComponents([
            'messages' => Messages::class,
        ]);
    }
}