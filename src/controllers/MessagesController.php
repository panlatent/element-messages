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
 * Class MessagesController
 *
 * @package panlatent\elementmessages\controllers
 * @author Panlatent <panlatent@gmail.com>
 */
class MessagesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @return Response
     */
    public function actionDeleteMessage(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
        $this->requirePermission(Permissions::MANAGE_MESSAGES);

        $id = Craft::$app->getRequest()->getBodyParam('id');

        if (!Plugin::getInstance()->getMessages()->deleteMessageById((int)$id)) {
            return $this->asJson([
                'success' => false
            ]);
        }

        return $this->asJson([
            'success' => true
        ]);
    }
}