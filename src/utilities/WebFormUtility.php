<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\utilities;

use tungsten\webform\WebForm;

use Craft;
use craft\base\Utility;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class WebFormUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('webform', 'WebForm');
    }

    /**
     * Returns the utility’s unique identifier.
     *
     * The ID should be in `kebab-case`, as it will be visible in the URL (`admin/utilities/the-handle`).
     *
     * @return string
     */
    public static function id(): string
    {
        return 'webform-utility';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias('@tungsten/webform/assetbundles/webformcp/dist/img/utility-icon.svg');
    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return WebForm::$plugin->webFormService->getSubmissionsCount('test');
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public static function contentHtml(): string
    {

        $view = Craft::$app->getView();

        $testSubmissionsCount = WebForm::$plugin->webFormService->getSubmissionsCount('test');

        return $view->renderTemplate(
            'webform/_components/utilities/WebFormUtility_content',
            [
                'testSubmissionsCount' => $testSubmissionsCount
            ]
        );
    }
}
