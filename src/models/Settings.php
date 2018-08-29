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
 * WebForm Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Tungsten Creative
 * @package   WebForm
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
            ['testWithMailtrap', 'boolean'],
            ['testWithMailtrap', 'default', 'value' => false],
        ];
    }
}
