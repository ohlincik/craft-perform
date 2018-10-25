<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\assetbundles\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Control Panel AssetBundle
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class PerFormCPAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@perfectus/perform/assetbundles/cp/dist';

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
