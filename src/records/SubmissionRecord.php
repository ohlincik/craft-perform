<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

class SubmissionRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%webform_submissions}}';
    }

    /**
     * Returns the redirect’s element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }
}
