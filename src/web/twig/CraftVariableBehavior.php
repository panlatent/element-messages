<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\web\twig;

use Craft;
use panlatent\elementmessages\db\MessageQuery;
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
    public Plugin $elementmessages;

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

    public function messages(array $criteria = []): MessageQuery
    {
        $query = new MessageQuery();
        Craft::configure($query, $criteria);
        return $query;
    }
}