<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages;

use Craft;
use panlatent\elementmessages\plugin\Services;

/**
 * Class Plugin
 *
 * @package panlatent\elementmessages
 * @author Panlatent <panlatent@gmail.com>
 */
class Plugin extends \craft\base\Plugin
{
    // Traits
    // =========================================================================

    use Services;

    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $schemaVersion = '0.1.0';

    // Public Methods
    // =========================================================================

    /**
     * Init the plugin.
     */
    public function init()
    {
        parent::init();
        Craft::setAlias('@panlatent/elementmessages', $this->getBasePath());

        $this->_setComponents();
    }
}