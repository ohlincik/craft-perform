<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\services;

use tungsten\webform\WebForm;
use tungsten\webform\elements\Submission;
use tungsten\webform\models\SubmissionModel;

use Craft;
use craft\db\Query;
use craft\base\Component;

/**
 * WebFormService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class WebFormService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a submission
     *
     * @return submission
     */
    public function getSubmissionById(int $submissionId, int $siteId = null)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return \Craft::$app->getElements()->getElementById($submissionId, Submission::class, $siteId);
    }

    /**
     * Add a submission
     *
     * @param array
     * @param boolean
     * @return boolean
     */
    public function addSubmission($submissionData, $isTestSubmission = false)
    {
        $submission = new Submission();

        $submission->statusType = $isTestSubmission ? 'test' : 'new';
        $submission->formHandle = $submissionData->formHandle;
        $submission->formTitle  = $submissionData->formTitle;
        $submission->subject    = $submissionData->getSubject();
        $submission->recipients = $submissionData->recipients;
        $submission->content    = $submissionData->getSerializedFields();

        $success = \Craft::$app->getElements()->saveElement($submission, true, false);

        if (!$success) {
            Craft::error('Couldn’t save the form submission "'.$submission->formHandle.'"', __METHOD__);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Set a submission status
     *
     * @param submission
     * @param string
     * @return boolean
     */
    public function setSubmissionStatusType($submission, $statusType) {
        $submission->statusType = $statusType;
        return \Craft::$app->getElements()->saveElement($submission, true, false);
    }

    /**
     * Returns the number of submissions of the specified $statusType.
     *
     * @param string $statusType
     * @return integer
     */
    public function getSubmissionsCount($statusType = null): int
    {
        if ($statusType === null) {
            $submissionsCount = Submission::find()->count();
        } else {
            $submissionsCount = Submission::find()
                ->statusType($statusType)
                ->count();
        }

        return (int) $submissionsCount;
    }

    public function deleteAllTestSubmissions()
    {
        $submissions = Submission::find()
            ->statusType('test')
            ->all();

        foreach ($submissions as $submission) {
            \Craft::$app->getElements()->deleteElement($submission);
        }
    }
}
