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
