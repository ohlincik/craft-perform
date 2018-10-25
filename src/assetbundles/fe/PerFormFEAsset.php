<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\assetbundles\fe;

use perfectus\perform\PerForm;

use craft\web\AssetBundle;

/**
 * Front-End AssetBundle
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class PerFormFEAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@perfectus/perform/assetbundles/fe/dist';

        // Only include the Parsley JS if it is enabled in plugin settings
        if (PerForm::$plugin->getSettings()->parsleyClientSideValidation) {
            $this->js = [
                'js/parsley.2.8.1.min.js',
            ];
        }

        $this->css = [
            'css/styles.css',
        ];

        parent::init();
    }
}
