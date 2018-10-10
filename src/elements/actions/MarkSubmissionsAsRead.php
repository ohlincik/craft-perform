<?php
/**
 * WebForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace tungsten\webform\elements\actions;

use tungsten\webform\WebForm;

use Craft;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;

use yii\base\Exception;

/**
 * @author    Oto Hlincik
 * @package   WebForm
 * @since     1.0.0
 *
 * @property string $triggerLabel
 */
class MarkSubmissionsAsRead extends ElementAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('app', 'Mark as Read…');
    }

    /**
     * Performs the action on any elements that match the given criteria.
     *
     * @param ElementQueryInterface $query The element query defining which elements the action should affect.
     *
     * @return bool Whether the action was performed successfully.
     * @throws \Throwable
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        // TODO: Figure out how to properly run the loop with submission elements
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
