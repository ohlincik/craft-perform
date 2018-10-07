<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\variables;

use tungsten\webform\WebForm;
use tungsten\webform\helpers\PluginTemplate as PluginTemplateHelper;

/**
 * WebForm Variable
 *
 * e.g. {{ craft.webForm }}
 *
 * @author    Tungsten Creative
 * @package   WebForm
 * @since     1.0.0
 */
class WebFormVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Render opening tag for the submission form.
     *
     * {{ craft.webForm.formTag(options) }}
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

        if (!WebForm::$plugin->webFormService->formSettingsValid($entryId)) {
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

        $pluginSettings = WebForm::$plugin->getSettings();

        return PluginTemplateHelper::renderPluginTemplate(
            '_components/variables/formTag',
            [
                'entryId' => $entryId,
                'parsleyClientSideValidation' => $pluginSettings->parsleyClientSideValidation,
                'parsleyValidationJsOptions' => $parsleyValidationJsOptions,
                'googleInvisibleCaptcha' => $pluginSettings->googleInvisibleCaptcha,
                'googleCaptchaSiteKey' => $pluginSettings->googleCaptchaSiteKey,
            ]
        );
    }
}
