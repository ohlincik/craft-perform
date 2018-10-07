<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\assetbundles\WebForm;

use tungsten\webform\WebForm;

use craft\web\AssetBundle;

/**
 * WebFormAsset AssetBundle
 *
 * This asset bundle is used for the plugin front-end.
 *
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class WebFormAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@tungsten/webform/assetbundles/webform/dist';

        // Only include the Parsley JS if it is enabled in plugin settings
        if (WebForm::$plugin->getSettings()->parsleyClientSideValidation) {
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
