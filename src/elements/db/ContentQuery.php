<?php

namespace panlatent\elementmessages\elements\db;

use craft\elements\db\ElementQuery;

class ContentQuery extends ElementQuery
{
    public ?int $channelId = null;

    public $fromTo;

    public $sendTo;

    public function __construct(array $config = [])
    {
        parent::__construct('', $config);
    }

    public function channel($value): self
    {
        return $this;
    }

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



}