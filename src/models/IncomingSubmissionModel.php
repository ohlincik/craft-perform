<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\models;

use craft\base\Model;

/**
 * Incoming Submission Model
 *
 * Stores the incoming submission data and provides methods to retrieve the right
 * information in the right format to process and save the submission.
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.1
 *
 * @property string $subject
 * @property array $variablesForEmailContent
 * @property string $serializedFields
 */
class IncomingSubmissionModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Form Handle
     *
     * @var string
     */
    public $formHandle;

    /**
     * Form Title
     *
     * @var string
     */
    public $formTitle;

    /**
     * Twig template for the Form Subject
     * e.g. "Contact form submitted by {{ name.value }}"
     *
     * @var string
     */
    public $subjectTemplate;

    /**
     * Twig template to extract the Reply To email address
     * e.g. "{{ email.value }}"
     *
     * @var string
     */
    public $replyTo;

    /**
     * Submission notification recipients. Emails separated by comma.
     * e.g. "user1@domain.com, user2@domain.com"
     *
     * @var string
     */
    public $recipients;

    /**
     * Fields submitted (posted) through the form
     *
     * @var array
     */
    public $fields;

    // Public Methods
    // =========================================================================

    /**
     * Returns the submission fields serialized for storing in the database
     *
     * @return string
     */
    public function getSerializedFields(): string
    {
        return serialize($this->fields);
    }

    /**
     * Returns the rendered submission subject
     *
     * @return string
     */
    public function getSubject(): string
    {
        $subject = \Craft::$app->view->renderString($this->subjectTemplate, $this->fields);
        $subject = empty($subject) ? 'Website Form Submission' : $subject;

        return $subject;
    }

    /**
     * Returns the individual notification recipient emails
     *
     * @return array
     */
    public function getRecipients(): array
    {
        return explode(',', str_replace(' ', '', $this->recipients));
    }

    /**
     * Returns the ReplyTo email address or false if empty
     *
     * @return string|bool
     */
    public function getReplyTo()
    {

        if (empty($this->replyTo)) {
            return false;
        }

        return \Craft::$app->view->renderString($this->replyTo, $this->fields);
    }

    /**
     * Returns the variables for rendering the email notification body
     *
     * @return array
     */
    public function getVariablesForEmailContent(): array
    {
        return [
            'subject' => $this->getSubject(),
            'fields'  => $this->fields,
        ];
    }

//    This is just a placeholder if server side validation is necessary
//    private function simpleValidation()
//    {
//        $errors = [];
//        foreach ($this->fields as $field) {
//            if (isset($field['required']) && (bool)$field['required'] === true && empty($field['value'])) {
//                $errors[] = "{$field['label']} is required.";
//            }
//        }
//
//        if ($errors) {
//            \Craft::$app->getSession()->setError(Craft::t('perform', 'There was a problem with your submission, please check the form and try again!'));
//            \Craft::$app->getUrlManager()->setRouteParams([
//                'variables' => ['performErrors' => $errors]
//            ]);
//
//            return null;
//        }
//    }
}
