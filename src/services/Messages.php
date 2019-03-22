<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\services;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Db;
use craft\helpers\Json;
use panlatent\elementmessages\errors\MessageException;
use panlatent\elementmessages\events\MessageEvent;
use panlatent\elementmessages\models\Message;
use panlatent\elementmessages\models\MessageCriteria;
use panlatent\elementmessages\records\Message as MessageRecord;
use yii\base\Component;
use yii\db\Query;

/**
 * Class Messages
 *
 * @package panlatent\elementmessages\services
 * @author Panlatent <panlatent@gmail.com>
 */
class Messages extends Component
{
    /**
     * @event MessageEvent The event that is triggered before a message is saved.
     */
    const EVENT_BEFORE_SAVE_MESSAGE = 'beforeSaveMessage';

    /**
     * @event MessageEvent The event that is triggered after a message is saved.
     */
    const EVENT_AFTER_SAVE_MESSAGE = 'afterSaveMessage';

    /**
     * @event MessageEvent The event that is triggered before a message is deleted.
     */
    const EVENT_BEFORE_DELETE_MESSAGE = 'beforeDeleteMessage';

    /**
     * @event MessageEvent The event that is triggered after a message is deleted.
     */
    const EVENT_AFTER_DELETE_MESSAGE = 'afterDeleteMessage';

    /**
     * Find a message by criteria.
     *
     * @param mixed $criteria
     * @return Message|null
     */
    public function findMessage($criteria = null)
    {
        if (!($criteria instanceof MessageCriteria)) {
            $criteria = new MessageCriteria($criteria);
        }

        $criteria->limit = 1;
        $messages = $this->findMessages($criteria);

        if (is_array($messages) && !empty($messages)) {
            return array_pop($messages);
        }

        return null;
    }

    /**
     * Find messages by criteria.
     *
     * @param mixed $criteria
     * @return Message[]
     */
    public function findMessages($criteria = null): array
    {
        if (!($criteria instanceof MessageCriteria)) {
            $criteria = new MessageCriteria($criteria);
        }

        $query = $this->_createQuery();

        $this->_applyMessageConditions($query, $criteria);

        if ($criteria->order) {
            $query->orderBy($criteria->order);
        }

        if ($criteria->offset) {
            $query->offset($criteria->offset);
        }

        if ($criteria->limit) {
            $query->limit($criteria->limit);
        }

        $results = $query->all();
        $messages = [];

        foreach ($results as $result) {
            $message = new Message($result);
            $messages[$message->id] = $message;
        }

        return $messages;
    }

    /**
     * Gets the total number of messages that match a given criteria.
     *
     * @param mixed $criteria
     * @return int
     */
    public function getTotalMessages($criteria): int
    {
        if (!($criteria instanceof MessageCriteria)) {
            $criteria = new MessageCriteria($criteria);
        }

        $query = (new Query())
            ->from(['{{%messages}}']);

        $this->_applyMessageConditions($query, $criteria);

        return (int)$query->count('[[messages.id]]');
    }

    /**
     * Create a message.
     *
     * @param mixed $config
     * @return Message
     */
    public function createMessage($config): Message
    {
        return new Message($config);
    }

    /**
     * Save a message.
     *
     * @param Message $message
     * @param bool $runValidation
     * @return bool
     */
    public function saveMessage(Message $message, bool $runValidation = true): bool
    {
        $isNewMessage = $message->getIsNew();

        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_MESSAGE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_MESSAGE, new MessageEvent([
                'message' => $message,
                'isNew' => $isNewMessage,
            ]));
        }

        if (!$message->beforeSave($isNewMessage)) {
            return false;
        }

        if ($runValidation && !$message->validate()) {
            Craft::info('Message not saved due to validation error.', __METHOD__);
            return false;
        }

        $db = Craft::$app->getDb();

        $transaction = $db->beginTransaction();
        try {
            if (!$isNewMessage) {
                $record =  MessageRecord::findOne(['id' => $message->id]);
                if (!$record) {
                    throw new MessageException();
                }
            } else {
                $record = new MessageRecord();
            }

            if ($message->contentId === null) {
                $content = $message->getContent();

                /** @var Element $content */
                if ($content instanceof ElementInterface) {
                    if (!$content->id) {
                        if (!Craft::$app->getElements()->saveElement($content)) {
                            throw new MessageException('Do not save content element due: ' . Json::encode($content->getErrors()));
                        }
                    }
                    $message->contentId = $content->id;
                }
            }

            $record->senderId = $message->senderId;
            $record->targetId = $message->targetId;
            $record->contentId = $message->contentId;
            $record->postDate = $message->getPostDate();

            $record->save(false);

            if ($isNewMessage) {
                $message->id = $record->id;
            }

            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $message->afterSave($isNewMessage);

        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_MESSAGE)) {
            $this->trigger(self::EVENT_AFTER_SAVE_MESSAGE, new MessageEvent([
                'message' => $message,
                'isNew' => $isNewMessage,
            ]));
        }

        return true;
    }


    /**
     * Applies WHERE conditions to a DbCommand query for messages.
     *
     * @param Query $query
     * @param MessageCriteria $criteria
     */
    private function _applyMessageConditions(Query $query, MessageCriteria $criteria)
    {
        if ($criteria->id) {
            $query->andWhere(Db::parseParam('id', $criteria->id));
        }

        if ($criteria->senderId) {
            $query->andWhere(Db::parseParam('senderId', $criteria->senderId));
        }

        if ($criteria->targetId) {
            $query->andWhere(Db::parseParam('targetId', $criteria->targetId));
        }

        if ($criteria->contentId) {
            $query->andWhere(Db::parseParam('contentId', $criteria->contentId));
        }

        if ($criteria->firstId) {
            $benchmark = $this->_createQuery()
                ->select(['postDate', 'sortOrder'])
                ->where(['id' => $criteria->firstId])
                ->one();

            $query->andWhere([ '>=', 'postDate', $benchmark['postDate']])
                ->andWhere(['>=', 'sortOrder', (int)$benchmark['sortOrder']])
                ->andWhere(Db::parseParam('id', $criteria->firstId, '!='));
        }

        if ($criteria->lastId) {
            $benchmark = $this->_createQuery()
                ->select(['postDate', 'sortOrder'])
                ->where(['id' => $criteria->lastId])
                ->one();

            $query->andWhere([ '<=', 'postDate', $benchmark['postDate']])
                ->andWhere(['<=', 'sortOrder', (int)$benchmark['sortOrder']])
                ->andWhere(Db::parseParam('id', $criteria->lastId, '!='));
        }

        if ($criteria->senderType) {
            $query->leftJoin('{{%elements}} elements', '[[elements.id]] = [[messages.senderId]]');
            $query->andWhere(Db::parseParam('elements.type', $criteria->senderType));
        }

        if ($criteria->targetType) {
            $query->leftJoin('{{%elements}} elements', '[[elements.id]] = [[messages.targetId]]');
            $query->andWhere(Db::parseParam('elements.type', $criteria->targetType));
        }

        if ($criteria->contentType) {
            $query->leftJoin('{{%elements}} elements', '[[elements.id]] = [[messages.contentId]]');
            $query->andWhere(Db::parseParam('elements.type', $criteria->contentType));
        }

        if ($criteria->uid) {
            $query->andWhere(Db::parseParam('uid', $criteria->uid));
        }
    }

    /**
     * @return Query
     */
    private function _createQuery(): Query
    {
        return (new Query())
            ->select(['id', 'senderId', 'targetId', 'contentId', 'postDate'])
            ->from('{{%messages}}');
    }
}