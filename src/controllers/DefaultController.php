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
use craft\helpers\UrlHelper;
use yii\web\NotFoundHttpException;

/**
 * @author    Oto Hlincik
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
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

    /**
     * Show Form Submission
     *
     * e.g.: actions/webform/default/show-submission
     *
     * @param int|null $submissionId Id of the form submission to display
     * @return mixed
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
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

        if ($submissionId !== null) {
            $siteId = Craft::$app->request->get('siteId');

            if ($siteId == null) {
                $siteId = Craft::$app->getSites()->currentSite->id;
            }

            $submission = WebForm::$plugin->webFormService->getSubmissionById($submissionId, $siteId);

            if (!$submission) {
                throw new NotFoundHttpException('Submission not found');
            }

            if ($submission->statusType === 'new') {
                WebForm::$plugin->webFormService->setSubmissionStatusType($submission, 'read');
            }

            $variables = [
                'submissionId' => $submission->id,
                'statusType'   => $submission->status,
                'formHandle'   => $submission->formHandle,
                'formTitle'    => $submission->formTitle,
                'subject'      => $submission->subject,
                'recipients'   => $submission->recipients,
                'fields'       => unserialize($submission->content, ['allowed_classes' => false]),
                'submitted'    => $submission->dateCreated,
            ];
        } else {
            throw new NotFoundHttpException('Submission id was not provided');
        }

        return $this->renderTemplate('webform/show', $variables);
    }
}
