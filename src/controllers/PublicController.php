<?php /** @noinspection PhpUndefinedFieldInspection */

/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\controllers;

use perfectus\perform\models\SubmissionModel;
use perfectus\perform\PerForm;
use perfectus\perform\models\IncomingSubmissionModel;

use Craft;
use craft\web\Controller;

use yii\web\Response;

/**
 * Public Controller
 *
 * @author    Oto Hlincik
 * @package   PerForm
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
     * e.g.: actions/perform/public/submit-form
     *
     * @return null|Response
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\MissingComponentException
     * @throws \craft\web\twig\TemplateLoaderException
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSubmitForm()
    {
        $this->requirePostRequest();
        $plugin = PerForm::getInstance();
        $request = \Craft::$app->getRequest();
        $pluginSettings = $plugin->getSettings();

        $entryId = $request->getBodyParam('entry_id');
        $fields = $request->getBodyParam('fields');

        $entry = \Craft::$app->entries->getEntryById($entryId);

        $formSettings = $plugin->formService->getFormSettings($entry);

        $testModeEnabled = $formSettings->testModeEnabled;

        // Validate captcha if enabled
        if ($pluginSettings->googleInvisibleCaptcha) {
            $gRecaptchaResponse = \Craft::$app->request->post('g-recaptcha-response');
            $remoteIp = \Craft::$app->request->remoteIp;

          if (!$plugin->formService->validateCaptcha($gRecaptchaResponse, $remoteIp)) {
            // Captcha verification failed!
            header($_SERVER['SERVER_PROTOCOL'].' 418 I\'m a teapot');
            exit;
          }
        }

        $submissionData = new IncomingSubmissionModel();
        $submissionData->setAttributes([
            'formHandle'      => $formSettings->formHandle,
            'formTitle'       => $formSettings->formTitle,
            'subjectTemplate' => $formSettings->formSubject,
            'replyTo'         => $formSettings->notificationReplyTo,
            'recipients'      => $formSettings->notificationRecipients,
            'fields'          => $fields,
        ], false);

        // Store the submission element in the CMS
        $submissionElement = $plugin->formService->addSubmission($submissionData, $testModeEnabled);
        if (!$submissionElement) {
            \Craft::$app->getSession()->setError(Craft::t('perform', 'The form submission could not be saved.'));
            \Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['payload' => $submissionData]
            ]);

            return null;
        }

        // Deliver the notification to the recipients
        if (!$plugin->emailService->deliver($submissionData, $testModeEnabled)) {
            if (!\Craft::$app->getSession()->hasFlash('error')) {
                \Craft::$app->getSession()->setError(Craft::t('perform', 'The form submission email could not be delivered.'));
            }
            \Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['payload' => $submissionData]
            ]);

            return null;
        }

        $submission = new SubmissionModel($submissionElement);

        \Craft::$app->getSession()->setFlash('submissionId', $submission->submissionId);
        return $this->redirectToPostedUrl($submission);
    }
}
