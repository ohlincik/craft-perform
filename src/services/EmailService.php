<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\services;

use craft\models\MailSettings;
use tungsten\webform\WebForm;
use tungsten\webform\models\SubmissionModel;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Template;
use craft\mail\Message;
use craft\web\View;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class EmailService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Deliver the submission in an email
     *
     * WebForm::$plugin->emailService->deliver()
     *
     * @param SubmissionModel $submissionData
     * @param bool $useTestMailer
     * @return bool
     * @throws \Twig_Error_Loader
     * @throws \craft\web\twig\TemplateLoaderException
     * @throws \yii\base\Exception
     */
    public function deliver(SubmissionModel $submissionData, bool $useTestMailer = false): bool
    {
        $mailerSettings = \Craft::$app->systemSettings->getEmailSettings();

        $message = $this->prepareMessage($submissionData, $mailerSettings);

        if ($useTestMailer)
        {
            $mailer = $this->getTestMailer($mailerSettings);

            if ($mailer === null) {
                return false;
            }

            return $mailer->send($message);
        }

        return \Craft::$app->mailer->send($message);
    }

    /**
     * @param SubmissionModel $submissionData
     * @param $mailerSettings
     * @return Message
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function prepareMessage(SubmissionModel $submissionData, MailSettings $mailerSettings): Message
    {
        $message = new Message();

        $message->setFrom([$mailerSettings['fromEmail'] => $mailerSettings['fromName']]);
        $message->setTo($submissionData->getRecipients());
        $message->setSubject($submissionData->getSubject());
        if ($submissionData->getReplyTo()) {
            $message->setReplyTo($submissionData->getReplyTo());
        }
        $message->setHtmlBody($this->renderHtmlBody($submissionData));

        return $message;
    }

    /**
     * @param SubmissionModel $submissionData
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function renderHtmlBody(SubmissionModel $submissionData): \Twig_Markup
    {
        $customEmailTemplate = $this->customEmailTemplate($submissionData->formHandle);

        if ($customEmailTemplate) {
            // Render the front-end email template
            $htmlBody = \Craft::$app->view->renderTemplate(
                $customEmailTemplate,
                $submissionData->getVariablesForEmailContent()
            );
        } else {
            \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

            // Render the default plugin email template
            $htmlBody = \Craft::$app->view->renderTemplate(
                'webform/_email/default',
                $submissionData->getVariablesForEmailContent()
            );

            \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);
        }

        return Template::raw($htmlBody);
    }

    private function getTestMailer($mailerSettings)
    {
        $pluginSettings = WebForm::$plugin->getSettings();

        if ($pluginSettings->testWithMailtrap)
        {
            $mailerSettings->transportSettings = [
                'host' => 'smtp.mailtrap.io',
                'port' => '2525',
                'useAuthentication' => true,
                'username' => $pluginSettings->testUsername,
                'password' => $pluginSettings->testPassword,
            ];

            $testMailerConfig = App::mailerConfig($mailerSettings);
            return Craft::createObject($testMailerConfig);
        }

        // WebForm Plugin exception
        return false;
    }

    /**
     * @param string $formHandle
     * @return bool|string
     */
    private function customEmailTemplate(string $formHandle)
    {
        $customEmailTemplatesPath = WebForm::$plugin->getSettings()->customEmailTemplatesPath;

        // Is the custom email template path set?
        if (!$customEmailTemplatesPath) {
            return false;
        }

        // Does the custom front-end email template exist?
        if (\Craft::$app->view->doesTemplateExist($customEmailTemplatesPath.'/'.$formHandle)) {
            return $customEmailTemplatesPath.'/'.$formHandle;
        }

        // Does the default front-end email template exist?
        if (\Craft::$app->view->doesTemplateExist($customEmailTemplatesPath.'/default')) {
            return $customEmailTemplatesPath.'/default';
        }

        return false;
    }
}
