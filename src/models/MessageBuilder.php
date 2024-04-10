<?php

namespace panlatent\elementmessages\models;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use DateTime;
use panlatent\elementmessages\Plugin;

class MessageBuilder
{
    protected ?ElementInterface $sender = null;

    protected ?ElementInterface $target = null;

    public function from(ElementInterface|ElementQuery|int $element): static
    {
        $this->sender = $this->getElement($element);
        return $this;
    }

    public function to(ElementInterface|ElementQuery|int $element): static
    {
        $this->target = $this->getElement($element);
        return $this;
    }

    public function send(ElementInterface|ElementQuery|int $element = null): bool
    {
        $message = new Message();
        $message->senderId = $this->sender->id;
        $message->targetId = $this->target->id;
        if ($element) {
            $message->contentId = $this->getElement($element)->id ?? null;
        }
        $message->postDate = new DateTime();

        if (!Plugin::getInstance()->getMessages()->saveMessage($message)) {
            var_dump($message->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @param ElementInterface|ElementQuery|int $element
     * @return ElementInterface
     */
    protected function getElement(ElementInterface|ElementQuery|int $element): ElementInterface
    {
        if ($element instanceof ElementQuery) {
            $element = $element->one();
        } elseif (is_int($element)) {
            $element = Craft::$app->getElements()->getElementById($element);
        }
        return $element;
    }
}