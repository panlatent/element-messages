<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages;

use Craft;
use craft\events\DefineBehaviorsEvent;
use craft\web\twig\variables\CraftVariable;
use panlatent\elementmessages\models\Settings;
use panlatent\elementmessages\plugin\Services;
use panlatent\elementmessages\user\Permissions;
use panlatent\elementmessages\web\twig\CraftVariableBehavior;
use yii\base\Event;

/**
 * Class Plugin
 *
 * @package panlatent\elementmessages
 * @property-read Settings $settings
 * @method Settings getSettings()
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
    public string $schemaVersion = '0.2.0';

    /**
     * @inheritdoc
     */
    public ?string $t9nCategory = 'elementmessages';

    // Public Methods
    // =========================================================================

    /**
     * Init the plugin.
     */
    public function init()
    {
        parent::init();
        Craft::setAlias('@elementmessages', $this->getBasePath());

        $this->_registerVariables();
        $this->_setComponents();
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): ?array
    {
        $ret =  parent::getCpNavItem();

        if ($this->getSettings()->cpNavName) {
            $ret['label'] = $this->getSettings()->cpNavName;
        } else {
            $ret['label'] = Craft::t('elementmessages', 'Messages');
        }

        $user = Craft::$app->getUser();

        if ($user->checkPermission(Permissions::MANAGE_MESSAGES)) {
            $ret['subnav']['messages'] = [
                'label' => Craft::t('elementmessages', 'Messages'),
                'url' => 'elementmessages/messages',
            ];
        }

        if ($user->checkPermission(Permissions::MANAGE_SETTINGS)) {
            $ret['subnav']['settings'] = [
                'label' => Craft::t('elementmessages', 'Settings'),
                'url' => 'elementmessages/settings',
            ];
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect('elementmessages/settings');
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    // Private Methods
    // =========================================================================

    /**
     * Register variables.
     */
    public function _registerVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = CraftVariableBehavior::class;
        });
    }
}