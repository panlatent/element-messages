<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\records;

use craft\db\ActiveRecord;
use DateTime;
use DateTimeZone;
use yii\db\Query;

/**
 * Class Message
 *
 * @package panlatent\elementmessages\records
 * @property int $id
 * @property int $senderId
 * @property int $targetId
 * @property int $contentId
 * @property DateTime $postDate
 * @property int $sortOrder
 * @author Panlatent <panlatent@gmail.com>
 */
class Message extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%messages}}';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            if ($this->sortOrder === null) {
                $lastSortOrder = (new Query())
                    ->select('sortOrder')
                    ->from('{{%messages}}')
                    ->where(['postDate' => $this->postDate->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s')])
                    ->orderBy(['sortOrder' => SORT_DESC])
                    ->scalar();

                $this->sortOrder = (int)$lastSortOrder + 1;
            }
        }

        return parent::beforeSave($insert);
    }
}