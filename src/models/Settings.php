<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\models;


use craft\base\Model;

/**
 * Class Settings
 *
 * @package panlatent\elementmessages\models
 * @author Panlatent <panlatent@gmail.com>
 */
class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string|null
     */
    public $cpNavName;

    /**
     * @var int
     */
    public $messagesPageSize = 50;

    /**
     * @var array
     */
    public $messagesPageCriteria = [
        'order' => [
            'postDate' => SORT_DESC,
            'sortOrder' => SORT_DESC
        ]
    ];
}