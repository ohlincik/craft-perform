<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\widgets;

use tungsten\webform\WebForm;
use tungsten\webform\assetbundles\webformcp\WebFormCPAsset;

use Craft;
use craft\base\Widget;

/**
 * WebForm Widget
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
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
        return Craft::getAlias("@tungsten/webform/assetbundles/webformcp/dist/img/widget-icon.svg");
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
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            []
        );
        return $rules;
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        return null;
    }

    /**
     * Returns the widget's body HTML.
     *
     * @return string|false The widget’s body HTML, or `false` if the widget
     *                      should not be visible. (If you don’t want the widget
     *                      to be selectable in the first place, use {@link isSelectable()}.)
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
