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

use Craft;
use craft\base\Component;
use tungsten\webform\elements\Submission;

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
        return Craft::$app->getElements()->getElementById($submissionId, Submission::class, $siteId);
    }

    /**
     * Add a submission
     *
     * @param array
     * @param boolean
     * @return boolean
     */
    public function addSubmission($submissionParams, $isTestSubmission = false)
    {
        $submission = new Submission();

        $submission->statusType = $isTestSubmission ? 'test' : 'new';
        $submission->formHandle = $submissionParams['formHandle'];
        $submission->formTitle  = $submissionParams['formTitle'];
        $submission->subject    = $submissionParams['subject'];
        $submission->recipients = $submissionParams['recipients'];
        $submission->content    = $submissionParams['content'];

        $success = Craft::$app->getElements()->saveElement($submission, true, false);

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
        return Craft::$app->getElements()->saveElement($submission, true, false);
    }
}
