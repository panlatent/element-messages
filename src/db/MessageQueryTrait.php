<?php

namespace panlatent\elementmessages\db;

trait MessageQueryTrait
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var int|null
     */
    public ?int $senderId = null;

    /**
     * @var int|null
     */
    public ?int $targetId = null;

    /**
     * @var int|null
     */
    public ?int $contentId = null;
}