<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\models;

use Craft;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\events\ModelEvent;
use craft\validators\DateTimeValidator;
use DateTime;
use panlatent\elementmessages\base\MessageTargetInterface;
use yii\base\InvalidConfigException;

/**
 * Class Message
 *
 * @package panlatent\elementmessages\models
 * @property-read bool $isNew
 * @property ElementInterface $sender
 * @property ElementInterface $target
 * @property ElementInterface|null $content
 * @author Panlatent <panlatent@gmail.com>
 */
class Message extends Model
{
    // Constants
    // =========================================================================

    // Events
    // -------------------------------------------------------------------------

    /**
     * @event ModelEvent
     * @see beforeSave()
     */
    const EVENT_BEFORE_SAVE = 'beforeSave';

    /**
     * @event ModelEvent
     * @see afterSave()
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    /**
     * @event ModelEvent
     * @see beforeDelete()
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * @event ModelEvent
     * @see afterDelete()
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    // Properties
    // =========================================================================

    /**
     * @var int|null
     */
    public $id;

    /**
     * @var int|null
     */
    public $senderId;

    /**
     * @var int|null
     */
    public $targetId;

    /**
     * @var int|null
     */
    public $contentId;

    /**
     * @var ElementInterface|null Save a new content element when a new message saving.
     */
    public $newContent;

    /**
     * @var DateTime|null
     */
    public ?DateTime $postDate = null;

    /**
     * @var ElementInterface|null
     */
    private $_sender;

    /**
     * @var ElementInterface|null
     */
    private $_target;

    /**
     * @var ElementInterface|null
     */
    private $_content;

    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getContent();
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['senderId', 'targetId', 'postDate'], 'required'];
        $rules[] = [['id', 'senderId', 'targetId', 'contentId'], 'integer'];
        $rules[] = [['postDate'], DateTimeValidator::class];

        return $rules;
    }

    /**
     * @return bool
     */
    public function getIsNew(): bool
    {
        return !$this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if ($this->contentId) {
            return Craft::t('elementmessages', '{sender} sends {content} to {target}', [
                'sender' => $this->getSender()::displayName(),
                'target' => $this->getTarget()::displayName(),
                'content' =>  $this->getContent()::displayName(),
            ]);
        }

        return Craft::t('elementmessages', '{sender} sends empty content to {target}', [
            'sender' => $this->getSender()::displayName(),
            'target' => $this->getTarget()::displayName(),
        ]);
    }

    /**
     * @return ElementInterface
     */
    public function getSender(): ElementInterface
    {
        if ($this->_sender !== null) {
            return $this->_sender;
        }

        if ($this->senderId === null) {
            throw new InvalidConfigException('Message is missing its sender ID');
        }

        if (($this->_sender = Craft::$app->getElements()->getElementById($this->senderId)) === null) {
            throw new InvalidConfigException('Invalid message sender ID');
        }

        return $this->_sender;
    }

    /**
     * @param ElementInterface $sender
     */
    public function setSender(ElementInterface $sender)
    {
        $this->_sender = $sender;
    }

    /**
     * @return ElementInterface
     */
    public function getTarget(): ElementInterface
    {
        if ($this->_target !== null) {
            return $this->_target;
        }

        if ($this->targetId === null) {
            throw new InvalidConfigException('Message is missing its target ID');
        }

        if (($this->_target = Craft::$app->getElements()->getElementById($this->targetId)) === null) {
            throw new InvalidConfigException('Invalid message target ID');
        }

        return $this->_target;
    }

    /**
     * @param ElementInterface $target
     */
    public function setTarget(ElementInterface $target)
    {
        $this->_target = $target;
    }

    /**
     * @return ElementInterface|null
     */
    public function getContent()
    {
        if ($this->_content !== null) {
            return $this->_content;
        }

        if ($this->contentId === null) {
            return null;
        }

        if (($this->_content = Craft::$app->getElements()->getElementById($this->contentId)) === null) {
            throw new InvalidConfigException('Invalid message content ID');
        }

        return $this->_content;
    }

    /**
     * @param ElementInterface|null $content
     */
    public function setContent(ElementInterface $content = null)
    {
        $this->_content = $content;
    }

    /**
     * @param bool $isNew
     * @return bool
     */
    public function beforeSave(bool $isNew): bool
    {
        $event = new ModelEvent([
            'isNew' => $isNew,
        ]);

        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        if (!$event->isValid) {
            return false;
        }

        if ($isNew) {
            $target = $this->getTarget();
            if ($target instanceof MessageTargetInterface && !$target->isAcceptableMessage($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew)
    {
        $target = $this->getTarget();
        if ($target instanceof MessageTargetInterface) {
            $target->acceptMessage($this);
        }

        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE)) {
            $this->trigger(self::EVENT_AFTER_SAVE, new ModelEvent([
                'isNew' => $isNew,
            ]));
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE)) {
            $event = new ModelEvent();

            $this->trigger(self::EVENT_BEFORE_DELETE, $event);

            return $event->isValid;
        }

        return true;
    }

    /**
     * After delete.
     */
    public function afterDelete()
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE)) {
            $this->trigger(self::EVENT_AFTER_DELETE, new ModelEvent());
        }
    }
}