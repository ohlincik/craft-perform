<?php /** @noinspection PropertyInitializationFlawsInspection */

/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform;

use perfectus\perform\services\FormService;
use perfectus\perform\services\EmailService;
use perfectus\perform\variables\PerFormVariable;
use perfectus\perform\models\Settings;
use perfectus\perform\fields\FormSettings as FormSettingsField;
use perfectus\perform\utilities\PerFormUtility;
use perfectus\perform\widgets\PerFormWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class PerForm
 *
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 *
 * @property  FormService $formService
 * @property  EmailService $emailService
 * @property mixed $cpNavItem
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class PerForm extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var PerForm
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'perform/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['perform/<submissionId:\d+>'] = 'perform/default/show-submission';
            }
        );

        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = FormSettingsField::class;
            }
        );

        // Register our utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = PerFormUtility::class;
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = PerFormWidget::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('perForm', PerFormVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    //TODO: Add post install screen
                }
            }
        );

        $this->setComponents([
            'formService' => FormService::class,
            'emailService' => EmailService::class,
        ]);

        // We're loaded
        Craft::info(
            Craft::t(
                'perform',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Set the badge count in the CP nav item to number of NEW submissions
     */
    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['badgeCount'] = self::$plugin->formService->getSubmissionsCount('new');
        return $item;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'perform/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
