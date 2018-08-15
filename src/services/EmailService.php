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
// use craft\helpers\App;
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
     *     WebForm::$plugin->emailService->exampleService()
     *
     * @param $html
     * @param $subject
     * @param null $recipients
     * @return bool
     */
    public function deliver($emailMessageParams): bool
    {
        $settings = \Craft::$app->systemSettings->getEmailSettings();

        $settings->transportSettings = [
            'host' => 'smtp.mailtrap.io',
            'port' => '2525',
            'useAuthentication' => '1',
            'username' => '9406d2a013cc9b',
            'password' => 'base64:Y3J5cHQ6TFEQgm1Fk3Orfw8LgM/dxTNjNWNiYmM1MzIxM2QyNDBlOTNiZTY2ZWEyYjEyNzMyZDNkOTg1M2VjMTgxOTY4MDBkOWNiODVmZGMzYWY0MDF+B/mplL0ZFLqiQmqdeZD1q2fT7y/A0E+9NsHwtjqacQ==',
        ];

        // Use this method of creating Mailer as of Craft 3.0.18
        // $mailer = App::mailerConfig($settings);

        // This method of creating Mailer is deprecated as of Craft 3.0.18
        $mailer = MailerHelper::createMailer($settings);

        $message = new Message();

        $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
        $message->setTo($emailMessageParams['recipients']);
        $message->setSubject($emailMessageParams['subject']);
        $message->setHtmlBody($this->renderHtmlBody($emailMessageParams));

        return Craft::$app->mailer->send($message);
        // return $mailer->send($message);
    }

    private function renderHtmlBody($emailMessageParams) {
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        // Render the default email template
        $htmlBody = \Craft::$app->view->renderTemplate('webform/_email/default', $emailMessageParams);

        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);

        return Template::raw($htmlBody);
    }
}
