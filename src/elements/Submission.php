<?php
namespace tungsten\webform\elements;

use tungsten\webform\WebForm;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use tungsten\webform\records\SubmissionRecord;
use tungsten\webform\elements\actions\DeleteSubmissions;
use craft\helpers\UrlHelper;

class Submission extends Element
{
    // Static
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('webform', 'Submission');
    }

    public static function refHandle()
    {
        return 'submission';
    }

    public static function hasContent(): bool
    {
        return false;
    }

    public static function hasTitles(): bool
    {
        return false;
    }

    public static function isLocalized(): bool
    {
        return true;
    }

    public static function hasStatuses(): bool
    {
        return false;
    }

    public static function find(): ElementQueryInterface
    {
        return new SubmissionQuery(static::class);
    }

    public function getIsEditable(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return (string)$this->formHandle;
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'formHandle' => \Craft::t('webform', 'Form'),
            'subject' => \Craft::t('webform', 'Subject'),
            'dateCreated' => \Craft::t('webform', 'Submitted'),
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        $attributes = ['formHandle', 'subject', 'dateCreated'];

        return $attributes;
    }

    protected static function defineSearchableAttributes(): array
    {
        return ['formHandle', 'subject'];
    }

    protected static function defineSortOptions(): array
    {
        return [
            'formHandle' => \Craft::t('webform', 'Form'),
            'subject' => \Craft::t('webform', 'Subject'),
            'elements.dateCreated' => \Craft::t('webform', 'Submitted'),
        ];
    }

    protected static function defineSources(string $context = null): array
    {


        return [
            [
                'key' => '*',
                'label' => Craft::t('webform', 'All Forms'),
                'criteria' => []
            ]
        ];
    }

    protected static function defineActions(string $source = null): array
    {
        // Delete
        $actions[] = DeleteSubmissions::class;
        return $actions;
    }

    public function getCpEditUrl()
    {
        return UrlHelper::cpUrl('webform/' . $this->id . '?siteId=' . $this->siteId);
        return $url;
    }

    public function afterSave(bool $isNew)
    {
        if (!$isNew) {
            $record = SubmissionRecord::findOne($this->id);
            if (!$record) {
                throw new Exception('Invalid submission ID: '.$this->id);
            }
        } else {
            $record = new SubmissionRecord();
            $record->id = $this->id;
        }
        $record->formHandle = $this->formHandle;
        $record->subject = $this->subject;
        $record->recipients = $this->recipients;
        $record->content = $this->content;
        $record->save(false);
        // remove form other sites
        // Craft::$app->getDb()->createCommand()
        //     ->delete('{{%elements_sites}}', [
        //         'AND',
        //         ['elementId' => $record->id],
        //         ['!=', 'siteId', $this->siteId]
        //     ])
        //     ->execute();
        parent::afterSave($isNew);
    }

    public function __toString()
    {
        try {
            return $this->getName();
        } catch (\Throwable $e) {
            ErrorHandler::convertExceptionToError($e);
        }
    }

    // Properties
    // =========================================================================

    public $formHandle;
    public $subject;
    public $recipients;
    public $content;
}
