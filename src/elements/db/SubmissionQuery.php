<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\elements;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 * @package tungsten\webform\elements
 */
class SubmissionQuery extends ElementQuery
{
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
     * @param $value
     * @return $this
     */
    public function statusType($value): self
    {
        $this->statusType = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function formHandle($value): self
    {
        $this->formHandle = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function formTitle($value): self
    {
        $this->formTitle = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function subject($value): self
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function recipients($value): self
    {
        $this->recipients = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function content($value): self
    {
        $this->content = $value;

        return $this;
    }

    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('webform_submissions');

        $this->query->select([
            'webform_submissions.statusType',
            'webform_submissions.formHandle',
            'webform_submissions.formTitle',
            'webform_submissions.subject',
            'webform_submissions.recipients',
            'webform_submissions.content',
        ]);

        if ($this->statusType) {
            $this->subQuery->andWhere(Db::parseParam('webform_submissions.statusType', $this->statusType));
        }

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
