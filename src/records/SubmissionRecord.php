<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\records;

use craft\base\Element;
use craft\db\ActiveRecord;

use yii\db\ActiveQueryInterface;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 *
 * @property \yii\db\ActiveQueryInterface $element
 */
class SubmissionRecord extends ActiveRecord
{
    /**
     * Form Submission Id
     *
     * @var integer
     */
    public $id;

    /**
     * Status Type
     *
     * @var string
     */
    public $statusType;

    /**
     * Form Handle
     *
     * @var string
     */
    public $formHandle;

    /**
     * Form Title
     *
     * @var string
     */
    public $formTitle;

    /**
     * Form Subject
     *
     * @var string
     */
    public $subject;

    /**
     * Submission notification recipients. Emails separated by comma.
     * e.g. "user1@domain.com, user2@domain.com"
     *
     * @var string
     */
    public $recipients;

    /**
     * Serialized form submission fields
     *
     * @var string
     */
    public $content;

    /**
     * Table Name
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%perform_submissions}}';
    }

    /**
     * Returns the form submission element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }
}
