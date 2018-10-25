<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\services;

use craft\elements\Entry;
use perfectus\perform\models\SubmissionModel;
use perfectus\perform\PerForm;
use perfectus\perform\elements\Submission;

use Craft;
use craft\base\Component;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class FormService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a submission
     *
     * @param int $submissionId
     * @param int|null $siteId
     * @return submission
     */
    public function getSubmissionById(int $submissionId, int $siteId = null): Submission
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return \Craft::$app->getElements()->getElementById($submissionId, Submission::class, $siteId);
    }

    /**
     * Add a submission
     *
     * @param SubmissionModel $submissionData
     * @param bool $isTestSubmission
     * @return boolean
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function addSubmission(SubmissionModel $submissionData, bool $isTestSubmission = false): bool
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
            Craft::error('Could not save the form submission "'.$submission->formHandle.'"', __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * Set a submission status
     *
     * @param Submission $submission
     * @param string
     * @return boolean
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function setSubmissionStatusType(Submission $submission, string $statusType): bool
    {
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

        return $submissionsCount;
    }

    /**
     * @throws \Throwable
     */
    public function deleteAllTestSubmissions()
    {
        $submissions = Submission::find()
            ->statusType('test')
            ->all();

        foreach ($submissions as $submission) {
            \Craft::$app->getElements()->deleteElement($submission);
        }
    }

    /**
     * @param $gRecaptchaResponse
     * @param $remoteIp
     * @return mixed
     */
    public function validateCaptcha($gRecaptchaResponse, $remoteIp) {
      $url = 'https://www.google.com/recaptcha/api/siteverify';

      $pluginSettings = PerForm::$plugin->getSettings();

      $data = array(
        'secret' => $pluginSettings->googleCaptchaSecretKey,
        'response' => $gRecaptchaResponse,
        'remoteip' => $remoteIp
      );

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      return json_decode(curl_exec($ch))->success;
    }

    /**
     * @param Entry $entry
     * @return bool|\craft\base\ElementInterface[]|mixed|null|string
     */
    public function getFormSettings(Entry $entry)
    {
        $entryFields = $entry->getFieldLayout()->getFields();

        $formSettingsHandle = false;

        foreach ($entryFields as $field) {
            if ($field::displayName() == 'Form Settings') {
                $formSettingsHandle = $field->handle;
            }
        }

        if ($formSettingsHandle) {
            $formSettings = $entry->$formSettingsHandle;
        } else {
            Craft::error(
                Craft::t(
                    'perform',
                    'Form Settings were not found in the specified Entry'
                ),
                __METHOD__
            );

            return false;
        }

        if (!$formSettings->validate()) {
            Craft::error(
                Craft::t(
                    'perform',
                    'Form Settings could not be validated. Errors: {errors}',
                    ['errors' => serialize($formSettings->errors)]
                ),
                __METHOD__
            );

            return false;
        }

        return $formSettings;
    }

    /**
     * @param $entryId
     * @return bool
     */
    public function formSettingsValid(int $entryId): bool
    {
        $entry = \Craft::$app->entries->getEntryById($entryId);

        if ($this->getFormSettings($entry)) {
            return true;
        }

        return false;
    }
}
