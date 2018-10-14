<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\migrations;

use tungsten\webform\elements\Submission;

use Craft;
use craft\db\Migration;

/**
 * WebForm Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        $this->removeRecordsFromElementsTable();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        // webform_submissions table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%webform_submissions}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%webform_submissions}}',
                [
                    'id' => $this->primaryKey(),
                    'statusType' => $this->string(255)->notNull()->defaultValue('new'),
                    'formHandle' => $this->string(255)->notNull()->defaultValue(''),
                    'formTitle' => $this->string(255)->notNull()->defaultValue(''),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'recipients' => $this->string(255)->notNull()->defaultValue(''),
                    'content' => $this->text(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // webform_submissions table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%webform_submissions}}', 'id'),
            '{{%webform_submissions}}',
            'id',
            '{{%elements}}',
            'id',
            'CASCADE',
            null
        );
    }

    protected function removeRecordsFromElementsTable()
    {
        $this->delete('{{%elements}}', ['type' => Submission::class]);
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // webform_submissions table
        $this->dropTableIfExists('{{%webform_submissions}}');
    }
}
