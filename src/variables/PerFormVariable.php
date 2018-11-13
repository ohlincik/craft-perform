<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\variables;

use perfectus\perform\PerForm;
use perfectus\perform\helpers\PluginTemplate as PluginTemplateHelper;
use perfectus\perform\models\SubmissionModel;
use yii\web\NotFoundHttpException;

/**
 * PerForm Variable
 *
 * e.g. {{ craft.perForm }}
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 */
class PerFormVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Render opening tag for the submission form.
     *
     * {{ craft.perForm.formTag(options) }}
     *
     * @param array $options
     * @return string
     */
    public function formTag(array $options = [])
    {
        // Minimum requirements
        if (!array_key_exists('entryId', $options) || !$options['entryId']) {
            return PluginTemplateHelper::renderPluginTemplate(
                '_components/variables/error',
                [
                    'error' => 'You need to provide the Entry ID where the plugin can locate the Form Settings field.',
                ]
            );
        }

        $entryId = $options['entryId'];

        if (!PerForm::$plugin->formService->formSettingsValid($entryId)) {
            return PluginTemplateHelper::renderPluginTemplate(
                '_components/variables/error',
                [
                    'error' => 'The Form Settings for the provided Entry ID are invalid. Please make the necessary adjustments and try again.',
                ]
            );
        }

        if (array_key_exists('parsleyValidationJsOptions', $options) && $options['parsleyValidationJsOptions']) {
            $parsleyValidationJsOptions = $options['parsleyValidationJsOptions'];
        } else {
            $parsleyValidationJsOptions = '';
        }

        $pluginSettings = PerForm::$plugin->getSettings();

        return PluginTemplateHelper::renderPluginTemplate(
            '_components/variables/formTag',
            [
                'entryId'                     => $entryId,
                'parsleyClientSideValidation' => $pluginSettings->parsleyClientSideValidation,
                'parsleyValidationJsOptions'  => $parsleyValidationJsOptions,
                'googleInvisibleCaptcha'      => $pluginSettings->googleInvisibleCaptcha,
                'googleCaptchaSiteKey'        => $pluginSettings->googleCaptchaSiteKey,
            ]
        );
    }

    /**
     * Retrieve the contents of an existing submission using the submission id
     *
     * {{ craft.perForm.getSubmissionById(submissionId) }}
     *
     * @param int|null $submissionId
     * @return SubmissionModel
     * @throws NotFoundHttpException
     */
    public function getSubmissionById(int $submissionId = null)
    {
        if ($submissionId !== null) {
            $submissionElement = PerForm::$plugin->formService->getSubmissionById($submissionId);

            if (!$submissionElement) {
                throw new NotFoundHttpException('Submission not found');
            }

            $submission = new SubmissionModel($submissionElement);
        } else {
            throw new NotFoundHttpException('Submission id was not provided');
        }

        return $submission;
    }
}
