<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\web\twig;

use panlatent\elementmessages\Plugin;
use yii\base\Behavior;

/**
 * Class CraftVariableBehavior
 *
 * @package panlatent\elementmessages\web\twig
 * @author Panlatent <panlatent@gmail.com>
 */
class CraftVariableBehavior extends Behavior
{
    // Properties
    // =========================================================================

    /**
     * @var Plugin
     */
    public $elementmessages;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->elementmessages = Plugin::getInstance();
    }
}