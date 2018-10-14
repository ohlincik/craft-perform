<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\widgets;

use tungsten\webform\WebForm;
use tungsten\webform\assetbundles\webformcp\WebFormCPAsset;

use Craft;
use craft\base\Widget;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 *
 * @property string|false $bodyHtml
 * @property null|string $settingsHtml
 */
class WebFormWidget extends Widget
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
        return Craft::t('webform', 'WebForm');
    }

    /**
     * Returns the path to the widget’s SVG icon.
     *
     * @return string|null The path to the widget’s SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias('@tungsten/webform/assetbundles/webformcp/dist/img/widget-icon.svg');
    }

    /**
     * Returns the widget’s maximum colspan.
     *
     * @return int|null The widget’s maximum colspan, if it has one
     */
    public static function maxColspan()
    {
        return 1;
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
        $rules = array_merge(
            $rules,
            []
        );
        return $rules;
    }

    /**
     * Returns the widget's body HTML.
     *
     * @return string|false The widget’s body HTML, or `false` if the widget
     *                      should not be visible. (If you don’t want the widget
     *                      to be selectable in the first place, use {@link isSelectable()}.)
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(WebFormCPAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'webform/_components/widgets/WebFormWidget_body',
            [
                'newSubmissionsCount' => WebForm::$plugin->webFormService->getSubmissionsCount('new'),
                'testSubmissionsCount' => WebForm::$plugin->webFormService->getSubmissionsCount('test'),
                'allSubmissionsCount' => WebForm::$plugin->webFormService->getSubmissionsCount(),
            ]
        );
    }
}
