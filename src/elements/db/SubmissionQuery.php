<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\elements;

use Craft;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use tungsten\webform\elements\Submission;

class SubmissionQuery extends ElementQuery
{
    /**
     * @var string|string[]|null The handle(s) that the resulting global sets must have.
     */
    public $formHandle;
    public $subject;
    public $recipients;
    public $content;

    public function formHandle($value)
    {
        $this->formHandle = $value;

        return $this;
    }

    public function formTitle($value)
    {
        $this->formTitle = $value;

        return $this;
    }

    public function subject($value)
    {
        $this->subject = $value;

        return $this;
    }

    public function recipients($value)
    {
        $this->recipients = $value;

        return $this;
    }

    public function content($value)
    {
        $this->content = $value;

        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('webform_submissions');

        $this->query->select([
            'webform_submissions.formHandle',
            'webform_submissions.formTitle',
            'webform_submissions.subject',
            'webform_submissions.recipients',
            'webform_submissions.content',
        ]);

        if ($this->formHandle) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.formHandle', $this->formHandle));
        }

        if ($this->formTitle) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.formTitle', $this->formTitle));
        }

        if ($this->subject) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.subject', $this->subject));
        }

        if ($this->recipients) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.recipients', $this->recipients));
        }

        if ($this->content) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.content', $this->content));
        }

        return parent::beforePrepare();
    }
}
