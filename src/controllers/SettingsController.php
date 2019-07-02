<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\controllers;

use Craft;
use craft\web\Controller;
use panlatent\elementmessages\Plugin;
use panlatent\elementmessages\user\Permissions;
use yii\web\Response;

/**
 * Class SettingsController
 *
 * @package panlatent\elementmessages\controllers
 * @author Panlatent <panlatent@gmail.com>
 */
class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @return Response|null
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();
        $this->requirePermission(Permissions::MANAGE_SETTINGS);

        $params = Craft::$app->getRequest()->getBodyParams();

        if (!Craft::$app->getPlugins()->savePluginSettings(Plugin::getInstance(), $params)) {
            Craft::$app->getSession()->setError(Craft::t('elementmessages', 'Couldn’t save settings.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'settings' => Plugin::getInstance()->getSettings(),
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('elementmessages', 'Settings saved.'));

        return $this->redirectToPostedUrl(Plugin::getInstance()->getSettings());
    }
}