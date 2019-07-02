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
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\helpers\Json;
use panlatent\elementmessages\errors\MessageException;
use panlatent\elementmessages\events\MessageEvent;
use panlatent\elementmessages\models\Message;
use panlatent\elementmessages\models\MessageCriteria;
use panlatent\elementmessages\records\Message as MessageRecord;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Query;

/**
 * Class Messages
 *
 * @package panlatent\elementmessages\services
 * @author Panlatent <panlatent@gmail.com>
 */
class Messages extends Component
{
    // Constants
    // =========================================================================

    // Events
    // -------------------------------------------------------------------------

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

    // Public Methods
    // =========================================================================

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
     * @param int $messageId
     * @return Message|null
     */
    public function getMessageById(int $messageId)
    {
        $result = $this->_createQuery()
            ->where(['id' => $messageId])
            ->one();

        return $result ? new Message($result) : null;
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

            if ($message->newContent) {
                /** @var Element $newContent */
                $newContent = $message->newContent;

                if (!Craft::$app->getElements()->saveElement($newContent)) {
                    throw new MessageException('Do not save content element due: ' . Json::encode($newContent->getErrors()));
                }

                $message->contentId = $newContent->id;
            }

            $record->senderId = $message->senderId;
            $record->targetId = $message->targetId;
            $record->contentId = $message->contentId;
            $record->postDate = DateTimeHelper::toDateTime($message->postDate);

            $record->save(false);

            if ($isNewMessage) {
                $message->id = $record->id;
            }

            $transaction->commit();
        } catch (Throwable $exception) {
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
     * @param int $messageId
     * @return bool
     */
    public function deleteMessageById(int $messageId): bool
    {
        $message = $this->getMessageById($messageId);
        if (!$message) {
            return false;
        }

        return $this->deleteMessage($message);
    }

    /**
     * Delete a message.
     *
     * @param Message $message
     * @return bool
     */
    public function deleteMessage(Message $message): bool
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_MESSAGE)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_MESSAGE, new MessageEvent([
                'message' => $message,
            ]));
        }

        if (!$message->beforeDelete()) {
            return false;
        }

        $db = Craft::$app->getDb();

        $transaction = $db->beginTransaction();
        try {
            $db->createCommand()
                ->delete('{{%messages}}', [
                    'id' => $message->id,
                ])
                ->execute();

            $message->afterDelete();

            $transaction->commit();
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_MESSAGE)) {
            $this->trigger(self::EVENT_AFTER_DELETE_MESSAGE, new MessageEvent([
                'message' => $message,
            ]));
        }

        return true;
    }

    // Private Methods
    // =========================================================================

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

        if ($criteria->bothOf) {
            if (!is_array($criteria->bothOf) || count($criteria->bothOf) != 2) {
                throw new InvalidConfigException("bothOf must be an array with 2 elements");
            }

            list($b1, $b2) = array_values($criteria->bothOf);
            $query->andWhere([
                'or',
                [
                    'senderId' => $b1,
                    'targetId' => $b2,
                ],
                [
                    'senderId' => $b2,
                    'targetId' => $b1,
                ]
            ]);
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
            $query->leftJoin('{{%elements}} s', '[[s.id]] = [[messages.senderId]]');
            $query->andWhere(Db::parseParam('s.type', $criteria->senderType));
        }

        if ($criteria->targetType) {
            $query->leftJoin('{{%elements}} t', '[[t.id]] = [[messages.targetId]]');
            $query->andWhere(Db::parseParam('t.type', $criteria->targetType));
        }

        if ($criteria->contentType) {
            $query->leftJoin('{{%elements}} c', '[[c.id]] = [[messages.contentId]]');
            $query->andWhere(Db::parseParam('c.type', $criteria->contentType));
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
            ->select([
                'messages.id',
                'messages.senderId',
                'messages.targetId',
                'messages.contentId',
                'messages.postDate'
            ])
            ->from(['messages' => '{{%messages}}']);
    }
}