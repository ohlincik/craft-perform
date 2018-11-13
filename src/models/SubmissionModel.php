<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\models;

use craft\base\Model;
use perfectus\perform\elements\Submission;

/**
 * Submission Model
 *
 * Contains submission data and provides methods to retrieve the right
 * information in the right format.
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 *
 * @property string $subject
 * @property array $variablesForEmailContent
 * @property string $serializedFields
 */
class SubmissionModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Submission ID
     *
     * @var int
     */
    public $submissionId;

    /**
     * DateTime when the submission was created
     *
     * @var \DateTime|null
     */
    public $dateCreated;

    /**
     * DateTime when the submission was last updated
     *
     * @var \DateTime|null
     */
    public $dateUpdated;

    /**
     * Submission status
     * (new, read, test)
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
     * Submission Subject
     *
     * @var string
     */
    public $subject;

    /**
     * Reply To email address
     *
     * @var null|string
     */
    public $replyTo;

    /**
     * Submission notification recipients.
     *
     * @var array
     */
    public $recipients;

    /**
     * Fields stored with the submission
     *
     * @var array
     */
    public $fields;

    // Public Methods
    // =========================================================================

    /**
     * Assign the submission values based on the submission element
     *
     * @param Submission|null $submissionElement
     */
    public function __construct(Submission $submissionElement = null)
    {
        if ($submissionElement !== null) {
            $this->submissionId = $submissionElement->id;
            $this->dateCreated = $submissionElement->dateCreated;
            $this->dateUpdated = $submissionElement->dateUpdated;
            $this->statusType = $submissionElement->statusType;
            $this->formHandle = $submissionElement->formHandle;
            $this->formTitle = $submissionElement->formTitle;
            $this->subject = $submissionElement->subject;
            // $this->replyTo = $submissionElement->replyTo;
            $this->recipients = $this->getRecipients($submissionElement->recipients);
            $this->fields = $this->unserializeFields($submissionElement->content);
        }
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns the submission fields unserialized for display
     *
     * @param string $content
     * @return array
     */
    private function unserializeFields(string $content): array
    {
        return unserialize($content, ['allowed_classes' => false]);
    }

    /**
     * Returns the individual notification recipient emails
     *
     * @param string $recipients
     * @return array
     */
    private function getRecipients(string $recipients): array
    {
        return explode(',', str_replace(' ', '', $recipients));
    }
}
