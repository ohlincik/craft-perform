<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\controllers;

use perfectus\perform\PerForm;

use Craft;
use craft\web\Controller;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class UtilitiesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle a request to delete all test submissions
     *
     * URL: perform/utilities/delete-test-submissions
     */
    public function actionDeleteTestSubmissions()
    {
        $this->requirePermission('utility:perform-utility');
        $this->requirePostRequest();

        PerForm::$plugin->formService->deleteAllTestSubmissions();
        Craft::$app->getSession()->setNotice('Deleted all test submissions.');
    }
}
