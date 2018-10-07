<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\assetbundles\webformcp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * WebFormCPAsset AssetBundle
 *
 * This asset bundle is used for the plugin in control panel .
 *
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 */
class WebFormCPAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@tungsten/webform/assetbundles/webformcp/dist';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/styles.css',
        ];

        parent::init();
    }
}
