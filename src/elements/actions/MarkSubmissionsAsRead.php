<?php

namespace tungsten\webform\elements\actions;

use tungsten\webform\WebForm;

use Craft;
use craft\base\ElementAction;
use tungsten\webform\elements\Submission;
use craft\elements\db\ElementQueryInterface;
use yii\base\Exception;

class MarkSubmissionsAsRead extends ElementAction
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('app', 'Mark as Read…');
    }

    /**
     * @inheritdoc
     */
    public static function isDestructive(): bool
    {
        return false;
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
                if ($submission->statusType !== 'test') {
                    WebForm::$plugin->webFormService->setSubmissionStatusType($submission, 'read');
                }
            }
        } catch (Exception $exception) {
            $this->setMessage($exception->getMessage());

            return false;
        }

        $this->setMessage(Craft::t('webform', 'Submissions marked as Read.'));

        return true;
    }
}
