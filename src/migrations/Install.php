<?php
/**
 * Element Messages plugin for CraftCMS 3
 *
 * @link      https://panlatent.com/
 * @copyright Copyright (c) 2019 panlatent@gmail.com
 */

namespace panlatent\elementmessages\migrations;

use craft\db\Migration;

/**
 * Class Install
 *
 * @package panlatent\elementmessages\migrations
 * @author Panlatent <panlatent@gmail.com>
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Messages
        // =====================================================================

        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey(),
            'senderId' => $this->integer()->notNull(),
            'targetId' => $this->integer()->notNull(),
            'contentId' => $this->integer(),
            'postDate' => $this->dateTime()->notNull(),
            'sortOrder' => $this->smallInteger()->defaultValue(0),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(null, '{{%messages}}', 'senderId');
        $this->createIndex(null, '{{%messages}}', 'targetId');
        $this->createIndex(null, '{{%messages}}', 'contentId');
        $this->createIndex(null, '{{%messages}}', 'postDate');
        $this->createIndex(null, '{{%messages}}', ['postDate', 'sortOrder'], true);
        $this->createIndex(null, '{{%messages}}', ['targetId', 'sortOrder']);

        $this->addForeignKey(null, '{{%messages}}', 'senderId', '{{%elements}}', 'id', 'CASCADE');
        $this->addForeignKey(null, '{{%messages}}', 'targetId', '{{%elements}}', 'id', 'CASCADE');
        $this->addForeignKey(null, '{{%messages}}', 'contentId', '{{%elements}}', 'id', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%messages}}');
    }
}