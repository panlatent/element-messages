<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\models;

use craft\base\Model;
use panlatent\elementmessages\db\MessageQueryTrait;

/**
 * Class MessageCriteria
 *
 * @package panlatent\elementmessages\models
 * @author Panlatent <panlatent@gmail.com>
 */
class MessageCriteria extends Model
{
    // Traits
    // =========================================================================

    use MessageQueryTrait;

    // Properties
    // =========================================================================

    /**
     * @var int[]|null
     */
    public $bothOf;

    /**
     * @var int|null First message id
     */
    public $firstId;

    /**
     * @var int|null Last message id
     */
    public $lastId;

    /**
     * @var string[]|string|null
     */
    public $senderType;

    /**
     * @var string[]|string|null
     */
    public $contentType;

    /**
     * @var string[]|string|null
     */
    public $targetType;

    /**
     * @var string Order
     */
    public $order = ['postDate' => SORT_ASC, 'sortOrder' => SORT_ASC];

    /**
     * @var int|null Offset
     */
    public $offset;

    /**
     * @var int|null Limit
     */
    public $limit;

    /**
     * @var string|string[]|null
     */
    public $uid;
}