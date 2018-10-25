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
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Use Mailtrap service for testing email is true
     *
     * @var boolean
     */
    public $testWithMailtrap = false;

    /**
     * Username for sending test email submission
     *
     * @var string
     */
    public $testUsername = '';

    /**
     * Password for sending test email submission
     *
     * @var string
     */
    public $testPassword = '';

    /**
     * Enable/Disable client side Parsley JS Validation
     *
     * @var boolean
     */
    public $parsleyClientSideValidation = true;

    /**
     * Enable/Disable Google Invisible reCAPTCHA
     *
     * @var boolean
     */
    public $googleInvisibleCaptcha = false;

    /**
     * Google reCAPTCHA Site Key
     *
     * @var string
     */
    public $googleCaptchaSiteKey = '';

    /**
     * Google reCAPTCHA Secret Key
     *
     * @var string
     */
    public $googleCaptchaSecretKey = '';

    /**
     * Path to front-end email templates
     *
     * @var string
     */
    public $customEmailTemplatesPath = '';

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['testWithMailtrap', 'boolean'],
            ['testWithMailtrap', 'default', 'value' => false],
        ];
    }
}
