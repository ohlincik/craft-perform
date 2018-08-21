<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\controllers;

use tungsten\webform\WebForm;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use tungsten\webform\elements\Submission;

/**
 * Utilities Controller
 *
 * Handle the actions required by the WebForm Utility.
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class UtilitiesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle a request to delete all test submissions
     *
     * URL: webform/utilities/delete-test-submissions
     */
    public function actionDeleteTestSubmissions()
    {
        $this->requirePermission('utility:webform-utility');
        $this->requirePostRequest();

        WebForm::$plugin->webFormService->deleteAllTestSubmissions();
        Craft::$app->getSession()->setNotice('Deleted all test submissions.');
    }
}
