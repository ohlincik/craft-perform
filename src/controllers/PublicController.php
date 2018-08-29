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

    // Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access public
     */
    public $allowAnonymous;

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's actionSubmitForm URL,
     * e.g.: actions/webform/public/submit-form
     *
     * @return Response|null
     */
    public function actionSubmitForm()
    {
        $this->requirePostRequest();
        $plugin = WebForm::getInstance();
        $request = \Craft::$app->getRequest();
        $pluginSettings = $plugin->getSettings();

        $entryId = $request->getBodyParam('entry_id');
        $fields = $request->getBodyParam('fields');

        $entry = \Craft::$app->entries->getEntryById($entryId);
        $testModeEnabled = $entry->testModeEnabled;

        // Validate captcha if enabled
        if ($pluginSettings->googleInvisibleCaptcha) {
            $gRecaptchaResponse = \Craft::$app->request->post('g-recaptcha-response');
            $remoteIp = \Craft::$app->request->remoteIp;

          if (!$plugin->webFormService->validateCaptcha($gRecaptchaResponse, $remoteIp)) {
            // Captcha verification failed!
            header($_SERVER['SERVER_PROTOCOL'].' 418 I\'m a teapot');
            exit;
          }
        }

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
        $success = $plugin->webFormService->addSubmission($submissionData, $testModeEnabled);

        // Deliver the notification to the recipients
        if (!$plugin->emailService->deliver($submissionData, $testModeEnabled)) {
            \Craft::$app->getSession()->setError(Craft::t('webform', 'The WebForm submission email could not be delivered.'));
            \Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['payload' => $submissionData]
            ]);

            return null;
        }

        \Craft::$app->getSession()->setNotice(Craft::t('webform', 'WebForm submission was successfully completed'));
        return $this->redirectToPostedUrl($submissionData, $entry->url."/?success=✓");
    }
}
