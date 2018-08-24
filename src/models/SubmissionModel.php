<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\models;

use tungsten\webform\WebForm;

use Craft;
use craft\base\Model;

/**
 * Submission Model
 *
 * Stores the submission data and provides methods to retrieve the right
 * information in the right format.
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class SubmissionModel extends Model
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
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            // ['formHandle', 'string'],
            // ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }

    /**
     * Returns the submission fields serialized for storing in the database
     *
     * @return string
     */
    public function getSerializedFields()
    {
        return serialize($this->fields);
    }

    /**
     * Returns the rendered submission subject
     *
     * @return string
     */
    public function getSubject()
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
    public function getRecipients()
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
    public function getVariablesForEmailContent()
    {
        return [
            'subject' => $this->getSubject(),
            'fields'  => $this->fields,
        ];
    }

    // This is just a placeholder if server side validation is necessary
    private function simpleValidation()
    {
        $errors = [];
        foreach ($fields as $field) {
            if (isset($field['required']) && (bool)$field['required'] === true) {
                if (empty($field['value'])) {
                    $errors[] = "{$field['label']} is required.";
                }
            }
        }

        if ($errors) {
            \Craft::$app->getSession()->setError(Craft::t('webform', 'There was a problem with your submission, please check the form and try again!'));
            \Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['webformErrors' => $errors]
            ]);

            return null;
        }
    }
}
