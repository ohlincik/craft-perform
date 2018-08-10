<?php

namespace tungsten\webform\elements\actions;

use Craft;
use craft\base\ElementAction;
use tungsten\webform\elements\Submission;
use craft\elements\db\ElementQueryInterface;
use yii\base\Exception;

class DeleteSubmissions extends ElementAction
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('app', 'Delete…');
    }

    /**
     * @inheritdoc
     */
    public static function isDestructive(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('webform', 'Are you sure you want to delete the selected submissions?');
    }

    /**
     * Performs the action on any elements that match the given criteria.
     *
     * @param ElementQueryInterface $query The element query defining which elements the action should affect.
     *
     * @return bool Whether the action was performed successfully.
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        try {
            foreach ($query->all() as $submission) {

            Craft::$app->getElements()->deleteElement($submission);

            }
        } catch (Exception $exception) {
            $this->setMessage($exception->getMessage());

            return false;
        }

        $this->setMessage(Craft::t('redirect', 'Submissions deleted.'));

        return true;
    }
}
