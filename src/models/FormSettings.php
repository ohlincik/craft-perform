<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\models;

use craft\base\Model;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class FormSettings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The form handle
     */
    public $formHandle;

    /**
     * @var string The form title
     */
    public $formTitle;

    /**
     * @var string The form subject
     *
     * It can include twig variables referencing fields which will be rendered
     * as a part of the form subject.
     *
     * e.g. Submission from {{ firstName.value }} {{ lastName.value }}
     */
    public $formSubject;

    /**
     * @var string Email addresses of recipients separated by a comma
     */
    public $notificationRecipients;

    /**
     * @var string Email address where email replies will be sent
     *
     * This will be usually a twig variable referencing an email field.
     *
     * e.g. {{ email.value }}
     */
    public $notificationReplyTo;

    /**
     * @var bool Test mode is enabled if set to true
     */
    public $testModeEnabled = false;

    // Public Methods
    // =========================================================================

    /**
     * Create new settings bundle
     *
     * @param array $config
     *
     * @return null|FormSettings
     */
    public static function create(array $config = [])
    {
        return new self($config);
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'formHandle',
                    'formTitle',
                    'formSubject',
                    'notificationRecipients',
                ],
                'required'
            ],
            [
                [
                    'formHandle',
                    'formTitle',
                    'formSubject',
                    'notificationRecipients',
                    'notificationReplyTo',
                ],
                'string'
            ],
            ['testModeEnabled', 'boolean'],
        ];
    }
}
