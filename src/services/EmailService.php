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

use perfectus\perform\PerForm;
use perfectus\perform\models\IncomingSubmissionModel;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Template;
use craft\mail\Message;
use craft\mail\transportadapters\Smtp;
use craft\models\MailSettings;
use craft\web\View;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class EmailService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Deliver the submission in an email
     *
     * PerForm::$plugin->emailService->deliver()
     *
     * @param IncomingSubmissionModel $submissionData
     * @param bool $useTestMailer
     * @return bool
     * @throws \Twig_Error_Loader
     * @throws \craft\web\twig\TemplateLoaderException
     * @throws \yii\base\Exception
     */
    public function deliver(IncomingSubmissionModel $submissionData, bool $useTestMailer = false): bool
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
     * @param IncomingSubmissionModel $submissionData
     * @param $mailerSettings
     * @return Message
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function prepareMessage(IncomingSubmissionModel $submissionData, MailSettings $mailerSettings): Message
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
     * @param IncomingSubmissionModel $submissionData
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function renderHtmlBody(IncomingSubmissionModel $submissionData): \Twig_Markup
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
                'perform/_email/default',
                $submissionData->getVariablesForEmailContent()
            );

            \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);
        }

        return Template::raw($htmlBody);
    }

    private function getTestMailer($mailerSettings)
    {
        $pluginSettings = PerForm::$plugin->getSettings();

        if ($pluginSettings->testWithMailtrap)
        {
            $testMailerSettings = new MailSettings();
            $testMailerSettings->fromEmail = $mailerSettings->fromEmail;
            $testMailerSettings->fromName = $mailerSettings->fromName;
            $testMailerSettings->transportType = Smtp::class;
            $testMailerSettings->transportSettings = [
                'host'              => 'smtp.mailtrap.io',
                'port'              => '2525',
                'useAuthentication' => true,
                'username'          => $pluginSettings->testUsername,
                'password'          => $pluginSettings->testPassword,
            ];

            $testMailerConfig = App::mailerConfig($testMailerSettings);
            return Craft::createObject($testMailerConfig);
        }

        // PerForm Plugin exception
        \Craft::$app->getSession()->setError(Craft::t('perform', 'The test form submission email could not be delivered. Please make sure that the PerForm plugin settings for Email Delivery Testing are correct.'));
        return null;
    }

    /**
     * @param string $formHandle
     * @return bool|string
     */
    private function customEmailTemplate(string $formHandle)
    {
        $customEmailTemplatesPath = PerForm::$plugin->getSettings()->customEmailTemplatesPath;

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
