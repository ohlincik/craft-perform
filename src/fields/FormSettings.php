<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\fields;

use tungsten\webform\assetbundles\webformcp\WebFormCPAsset;
use tungsten\webform\models\FormSettings as FormSettingsModel;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 *
 * @property string $contentColumnType
 */
class FormSettings extends Field
{
    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('webform', 'Form Settings');
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
        ]);

        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        $settings = [];

        if (!empty($value)) {
            if (\is_string($value)) {
                // Decode any html entities
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
                $settings = Json::decodeIfJson($value);
            }
            if (\is_array($value)) {
                $settings = $value;
            }
        }

        return FormSettingsModel::create($settings);
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed $value The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $variables = [];
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(WebFormCPAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $nameSpacedId = Craft::$app->getView()->namespaceInputId($id);

        // Basic variables
        $variables['name'] = $this->handle;
        $variables['value'] = $value;
        $variables['field'] = $this;
        $variables['id'] = $id;
        $variables['nameSpacedId'] = $nameSpacedId;

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'webform/_components/fields/FormSettings_input',
            $variables
        );
    }
}
