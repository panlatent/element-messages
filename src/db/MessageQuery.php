<?php

namespace panlatent\elementmessages\db;

use craft\base\ElementInterface;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use panlatent\elementmessages\models\Message;

class MessageQuery extends Query
{
    // Traits
    // =========================================================================

    use MessageQueryTrait;

    // Properties
    // =========================================================================

    public $fromTo;

    public $sendTo;

    public function fromTo($value): self
    {
        $this->fromTo = $value;
        return $this;
    }

    public function sendTo($value): self
    {
        $this->sendTo = $value;
        return $this;
    }

    public function populate($rows): array
    {
        $elements = [];
        foreach ($rows as $row) {
            $elements[] = new Message($row);
        }
        return $elements;
    }

    public function prepare($builder): Query
    {
        if ($this->id) {
            $this->andWhere(Db::parseParam('id', $this->id));
        }

        if ($this->senderId) {
            $this->andWhere(Db::parseParam('senderId', $this->senderId));
        }

        if ($this->targetId) {
            $this->andWhere(Db::parseParam('targetId', $this->targetId));
        }

        if ($this->contentId) {
            $this->andWhere(Db::parseParam('contentId', $this->contentId));
        }

        if ($this->fromTo) {
            if (is_int($this->fromTo)) {
                $this->andWhere(Db::parseParam('senderId', $this->fromTo));
            } elseif ($this->fromTo instanceof ElementInterface) {
                $this->andWhere(Db::parseParam('senderId', $this->fromTo->id));
            } elseif ($this->fromTo instanceof ElementQuery) {
                $this->andWhere('senderId', $this->fromTo);
            }
        }

        $this->select([
            '[[messages.id]]',
            '[[messages.senderId]]',
            '[[messages.targetId]]',
            '[[messages.contentId]]',
            '[[messages.postDate]]',
        ])->from(['messages' => Table::MESSAGES]);

        return $this;
    }
}