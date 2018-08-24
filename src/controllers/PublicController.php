<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\controllers;

use tungsten\webform\WebForm;
use tungsten\webform\models\SubmissionModel;

use Craft;
use craft\web\Controller;

/**
 * Public Controller
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class PublicController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['submit-form'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's actionSubmitForm URL,
     * e.g.: actions/webform/public/submit-form
     *
     * @return mixed
     */
    public function actionSubmitForm()
    {
        $this->requirePostRequest();
        $entryId = \Craft::$app->request->post('entry_id');
        $fields = \Craft::$app->request->post('fields');

        $entry = \Craft::$app->entries->getEntryById($entryId);

        // Validate captcha if enabled
        // if ($captcha) {
        //     $gRecaptchaResponse = \Craft::$app->request->post('g-recaptcha-response');
        //     $remoteIp = \Craft::$app->request->remoteIp;

        //   if (!WebForm::$plugin->webFormService->validateCaptcha($gRecaptchaResponse, $remoteIp))
        //   {
        //     // Captcha verification failed!
        //     header($_SERVER['SERVER_PROTOCOL'].' 418 I\'m a teapot');
        //     exit;
        //   }
        // }

        $submissionData = new SubmissionModel();
        $submissionData->setAttributes([
            'formHandle'      => $entry->formHandle,
            'formTitle'       => $entry->formTitle,
            'subjectTemplate' => $entry->formSubject,
            'replyTo'         => $entry->notificationReplyTo,
            'recipients'      => $entry->notificationRecipients,
            'fields'          => $fields,
        ], false);

        // Store the submission element in the CMS if the setting is enabled
        $success = WebForm::$plugin->webFormService->addSubmission(
            $submissionData,
            $entry->testModeEnabled
        );

        // Deliver the notification to the recipients
        $emailSent = WebForm::$plugin->emailService->deliver(
            $submissionData,
            $entry->testModeEnabled
        );

        $redirectUrl = $entry->url."/?success=✓";

        // Redirect back to the form page
        $this->redirect($redirectUrl);
    }
}
