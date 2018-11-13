<?php

namespace perfectus\perform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m181113_151221_add_replyto_field migration.
 */
class m181113_151221_add_replyto_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "m181113_151221_add_replyto_field updating.\n";

        $this->addColumn('{{%perform_submissions}}', 'replyTo', $this->string()->after('recipients'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181113_151221_add_replyto_field cannot be reverted.\n";
        return false;
    }
}
