<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\utilities;

use perfectus\perform\PerForm;

use Craft;
use craft\base\Utility;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class PerFormUtility extends Utility
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
        return Craft::t('perform', 'PerForm');
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
        return 'perform-utility';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias('@perfectus/perform/assetbundles/cp/dist/img/utility-icon.svg');
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
        return PerForm::$plugin->formService->getSubmissionsCount('test');
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

        $testSubmissionsCount = PerForm::$plugin->formService->getSubmissionsCount('test');

        return $view->renderTemplate(
            'perform/_components/utilities/PerFormUtility_content',
            [
                'testSubmissionsCount' => $testSubmissionsCount
            ]
        );
    }
}
