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
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    // protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/webform/default
     *
     * @return mixed
     */
    // public function actionIndex()
    // {
    //     $result = 'Welcome to the DefaultController actionIndex() method';

    //     return $result;
    // }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/webform/default/do-something
     *
     * @return mixed
     */
    public function actionShowSubmission(int $submissionId = null): craft\web\Response
    {
        $variables = [];
        // Breadcrumbs
        $variables['crumbs'] = [
            [
                'label' => Craft::t('webform', 'WebForm Submissions'),
                'url' => UrlHelper::url('webform')
            ]
        ];
        // $editableSitesOptions = [
        // ];
        // foreach (Craft::$app->getSites()->getAllSites() as $site) {
        //     $editableSitesOptions[$site['id']] = $site->name;
        // }
        if ($submissionId !== null)
        {
            $siteId = Craft::$app->request->get('siteId');
            if ($siteId == null) {
                $siteId = Craft::$app->getSites()->currentSite->id;
            }
            $submission = WebForm::$plugin->webFormService->getSubmissionById($submissionId, $siteId);
            if (!$submission) {
                throw new NotFoundHttpException('Submission not found');
            }

            $variables = [
                'formHandle' => $submission->formHandle,
                'formTitle' => $submission->formTitle,
                'subject' => $submission->subject,
                'recipients' => $submission->recipients,
                'fields' => unserialize($submission->content),
                'submitted' => $submission->dateCreated,
            ];
        }
        else
        {
            throw new NotFoundHttpException('Submission id was not provided');
        }
        // $variables['currentSiteId'] = $redirect->siteId;
        return $this->renderTemplate('webform/show', $variables);
    }
}
