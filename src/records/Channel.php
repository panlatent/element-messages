<?php

namespace panlatent\elementmessages\records;

use craft\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 */
class Channel extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%messages_channels}}';
    }
}