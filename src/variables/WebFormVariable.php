<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      http://atomic74.com
 * @copyright Copyright (c) 2018 Tungsten Creative
 */

namespace tungsten\webform\variables;

use tungsten\webform\WebForm;
use craft\helpers\Template;
use craft\web\View;

use Craft;

/**
 * WebForm Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.webForm }}).
 *
 * https://craftcms.com/docs/plugins/variables
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
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.webForm.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.webForm.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function formTag($options = array())
    {
        // Minimum requirements
        if (!array_key_exists('entryId', $options) || !$options['entryId'])
            return "The Entry ID must be provided.";
        else
            $entryId = $options['entryId'];

        if (array_key_exists('parsleyValidationJsOptions', $options) && $options['parsleyValidationJsOptions']) {
            $parsleyValidationJsOptions = $options['parsleyValidationJsOptions'];
        } else {
            $parsleyValidationJsOptions = '';
        }

        $pluginSettings = WebForm::$plugin->getSettings();

        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        // Render the form tag template
        $formTag = \Craft::$app->view->renderTemplate('webform/formTag', [
            'entryId' => $entryId,
            'parsleyClientSideValidation' => $pluginSettings->parsleyClientSideValidation,
            'parsleyValidationJsOptions' => $parsleyValidationJsOptions,
            'googleInvisibleCaptcha' => $pluginSettings->googleInvisibleCaptcha,
            'googleCaptchaSiteKey' => $pluginSettings->googleCaptchaSiteKey,
        ]);

        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);

        // return TemplateHelper::getRaw($formTag);
        return Template::raw($formTag);
    }
}
