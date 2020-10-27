<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\elements;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 * @package perfectus\perform\elements
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
     * The email that has been captured from submission as 'Reply To'
     *
     * @var string
     */
    public $replyTo;

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
    public function replyTo($value): self
    {
        $this->replyTo = $value;

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
        $this->joinElementTable('perform_submissions');

        $this->query->select([
            'perform_submissions.statusType',
            'perform_submissions.formHandle',
            'perform_submissions.formTitle',
            'perform_submissions.subject',
            'perform_submissions.recipients',
            'perform_submissions.replyTo',
            'perform_submissions.content',
        ]);

        if ($this->statusType) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.statusType', $this->statusType));
        }

        if ($this->formHandle) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.formHandle', $this->formHandle));
        }

        if ($this->formTitle) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.formTitle', $this->formTitle));
        }

        if ($this->subject) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.subject', $this->subject));
        }

        if ($this->recipients) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.recipients', $this->recipients));
        }

        if ($this->replyTo) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.replyTo', $this->replyTo));
        }

        if ($this->content) {
            $this->subQuery->andWhere(Db::parseParam('perform_submissions.content', $this->content));
        }

        return parent::beforePrepare();
    }
}
