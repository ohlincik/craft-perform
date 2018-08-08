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

use Craft;
use craft\web\Controller;

/**
 * Public Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
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

        // Validate the submitted params

        // Store the form submission
        WebForm::$plugin->webFormService->addFormSubmission();
        // echo('<pre>');
        // var_dump($result);
        // echo('</pre>');
        exit();

        // $entry = \Craft::$app->entries->getEntryById($entryId);

        // $formHandle = $entry->formHandle;

        // // If captcha is enabled, verify that the request included the correct solution
        // if ($captcha) {
        //   $gRecaptchaResponse = craft()->request->getPost('g-recaptcha-response');
        //   $remoteIp = craft()->request->ipAddress;

        //   if (!$this->validateCaptcha($gRecaptchaResponse, $remoteIp))
        //   {
        //     // Captcha verification failed!
        //     header($_SERVER['SERVER_PROTOCOL'].' 418 I\'m a teapot');
        //     exit;
        //   }
        // }

        // $recipients = $entry->notificationRecipients;
        // $subject = $entry->notificationSubject;
        // $template = $template_dir.$formHandle.'-notification';
        $redirect_url = $entry->url."/?success=✓";

        // // If custom email template does not exist use the default template
        // if (!craft()->templates->doesTemplateExist($template))
        // {
        //   $template = $default_template;
        // }

        // // Build the email message
        // $message = new EmailModel();
        // $message->subject = $subject;

        // // Populate the Reply To setting if it exists
        // $replyTo = craft()->templates->renderString($entry->notificationReplyTo, $fields);
        // $replyTo = empty($replyTo) ? false : $replyTo;

        // // Create the html version of the content
        // $message->htmlBody = craft()->templates->render($template, array(
        //   'subject' => $subject,
        //   'fields'  => $fields
        // ));

        // // Save the form in the CMS if the setting is enabled
        // if ($saveInCms)
        // {
        //   $webFormModel = new WebFormModel();

        //   $webFormModel->handle = $formHandle;
        //   $webFormModel->recipients = $testMode ? '[-- test --], '.$recipients : $recipients;
        //   $webFormModel->subject = craft()->templates->renderString($entry->formSubject, $fields);
        //   $webFormModel->content = $message->htmlBody;

        //   craft()->webForm->addWebForm($webFormModel);
        // }

        // // If testMode mode is ON, display the form information on screen
        // if ($testMode)
        // {
        //   $pluginTemplatesPath = craft()->path->getPluginsPath().'webform/templates';
        //   craft()->path->setTemplatesPath($pluginTemplatesPath);
        //   echo craft()->templates->render('test-mode', array(
        //     'recipients' => $recipients,
        //     'subject' => $subject,
        //     'replyTo' => $replyTo ? $replyTo : '-- NOT SET --',
        //     'content' => $message->htmlBody
        //   ));
        //   exit();
        // }
        // else
        // {
        //   // Send email message to each recipient individually
        //   foreach (explode(',',$recipients) as $recipient)
        //   {
        //     try
        //     {
        //       $message->toEmail = trim($recipient);
        //       if ($replyTo)
        //       {
        //         $message->replyTo = $replyTo;
        //       }
        //       if (!craft()->email->sendEmail($message))
        //       {
        //         WebFormPlugin::log("Failed to send email for {$formHandle} form.", LogLevel::Error);
        //       }
        //     }
        //     catch (\Exception $e)
        //     {
        //       WebFormPlugin::log("Failed to send email for {$formHandle} form. Reason: ".$e->getMessage(), LogLevel::Error);
        //     }
        //   }

        //   // Redirect back to the form page
        //   $this->redirect($redirect_url);
        // }
    }
}
