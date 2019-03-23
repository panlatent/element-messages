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
use craft\helpers\DateTimeHelper;
use craft\validators\DateTimeValidator;
use panlatent\elementmessages\base\MessageTargetInterface;
use yii\base\InvalidConfigException;

/**
 * Class Message
 *
 * @package panlatent\elementmessages\models
 * @property-read bool $isNew
 * @property ElementInterface $sender
 * @property ElementInterface $target
 * @property \DateTime|null $postDate
 * @property ElementInterface|string|null $content
 * @author Panlatent <panlatent@gmail.com>
 */
class Message extends Model
{
    // Scenarios
    // =========================================================================
    const SCENARIO_CONTENT = 'content';
    const SCENARIO_NEW_CONTENT = 'newContent';

    // Constants
    // =========================================================================

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

    /**
     * @var \DateTime|null
     */
    private $_postDate;

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
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['senderId', 'targetId', 'postDate'], 'required'],
            [['contentId'], 'required', 'on' => self::SCENARIO_CONTENT],
            [['newContent'], 'required', 'on' => self::SCENARIO_NEW_CONTENT],
            [['id', 'senderId', 'targetId', 'contentId'], 'integer'],
            [['postDate'], DateTimeValidator::class],
        ]);

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
     * @return ElementInterface
     */
    public function getSender(): ElementInterface
    {
        if ($this->_sender !== null) {
            return $this->_sender;
        }

        if ($this->senderId === null) {
            throw new InvalidConfigException();
        }

        $this->_sender = Craft::$app->getElements()->getElementById($this->senderId);
        if ($this->_sender === null) {
            throw new InvalidConfigException();
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
            throw new InvalidConfigException();
        }

        $this->_target = Craft::$app->getElements()->getElementById($this->targetId);
        if ($this->_target === null) {
            throw new InvalidConfigException();
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
     * @return ElementInterface|string|null
     */
    public function getContent()
    {
        if ($this->_content !== null) {
            return $this->_content;
        }

        if (!$this->contentId) {
            throw new InvalidConfigException();
        }

        $this->_content = Craft::$app->getElements()->getElementById($this->contentId);
        if ($this->_content === null) {
            throw new InvalidConfigException();
        }

        return $this->_content;
    }

    /**
     * @param ElementInterface|string|null $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * @return \DateTime|null
     */
    public function getPostDate()
    {
        return $this->_postDate;
    }

    /**
     * @param mixed $value
     */
    public function setPostDate($value)
    {
        $this->_postDate = DateTimeHelper::toDateTime($value);
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
}