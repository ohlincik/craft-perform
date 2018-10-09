<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\controllers;

use tungsten\webform\WebForm;

use Craft;
use craft\web\Controller;

/**
 * @author    Oto Hlincik
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
