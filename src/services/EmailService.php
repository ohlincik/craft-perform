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
use tungsten\webform\models\SubmissionModel;

use Craft;
use craft\base\Component;
use craft\helpers\MailerHelper;
use craft\helpers\Template;
use craft\mail\Message;
use craft\web\View;

/**
 * EmailService Service
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
class EmailService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     WebForm::$plugin->emailService->deliver()
     *
     * @param $html
     * @param $subject
     * @param null $recipients
     * @return bool
     */
    public function deliver($submissionData, $useTestMailer = false): bool
    {
        $mailerSettings = \Craft::$app->systemSettings->getEmailSettings();

        $message = $this->prepareMessage($submissionData, $mailerSettings);

        if ($useTestMailer)
        {
            $mailer = $this->getTestMailer($mailerSettings);

            return $mailer->send($message);
        }
        else
        {
            return \Craft::$app->mailer->send($message);
        }
    }

    private function prepareMessage($submissionData, $mailerSettings)
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

    private function renderHtmlBody($submissionData) {
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        // Render the default email template
        $htmlBody = \Craft::$app->view->renderTemplate(
            'webform/_email/default',
            $submissionData->getVariablesForEmailContent()
        );

        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);

        return Template::raw($htmlBody);
    }

    private function getTestMailer($mailerSettings) {
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

            // Use this method of creating Mailer as of Craft 3.0.18
            // $mailer = App::mailerConfig($mailerSettings);

            // This method of creating Mailer is deprecated as of Craft 3.0.18
            return MailerHelper::createMailer($mailerSettings);
        } else {
            return null;
        }
    }
}
