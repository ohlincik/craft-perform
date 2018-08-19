<?php
namespace tungsten\webform\elements;

use tungsten\webform\WebForm;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use tungsten\webform\records\SubmissionRecord;
use tungsten\webform\elements\actions\DeleteSubmissions;
use tungsten\webform\elements\actions\MarkSubmissionsAsNew;
use tungsten\webform\elements\actions\MarkSubmissionsAsRead;
use craft\helpers\UrlHelper;

class Submission extends Element
{
    // Static
    // =========================================================================

    public static $statusColor = [
        'new'     => 'green',
        'read'    => 'grey',
        'test'    => 'orange',
    ];

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
        return (string)$this->subject;
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'subject' => \Craft::t('webform', 'Subject'),
            'statusType' => \Craft::t('webform', 'Status'),
            'dateCreated' => \Craft::t('webform', 'Submitted'),
            'formTitle' => \Craft::t('webform', 'Form Title'),
            'formHandle' => \Craft::t('webform', 'Form Handle'),
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return [
            'subject',
            'statusType',
            'dateCreated',
            'formTitle',
            'formHandle',
        ];
    }

    protected static function defineSearchableAttributes(): array
    {
        return [
            'subject',
            'formTitle',
            'formHandle',
        ];
    }

    protected static function defineSortOptions(): array
    {
        return [
            [
                'label' => Craft::t('webform', 'Submitted'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated'
            ],
            'subject' => \Craft::t('webform', 'Subject'),
            'statusType' => \Craft::t('webform', 'Status'),
            'formTitle' => \Craft::t('webform', 'Form Title'),
            'formHandle' => \Craft::t('webform', 'Form Handle'),
        ];
    }

    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => Craft::t('webform', 'All Submissions'),
                'criteria' => []
            ],
            [
                'key' => 'new',
                'label' => Craft::t('webform', 'NEW Submissions'),
                'criteria' => [
                    'statusType' => 'new',
                ]
            ],
            [
                'key' => 'read',
                'label' => Craft::t('webform', 'Read Submissions'),
                'criteria' => [
                    'statusType' => 'read',
                ]
            ],
        ];

        $testSubmissionsCount = WebForm::$plugin->webFormService->getSubmissionsCount('test');

        if ($testSubmissionsCount > 0) {
            $sources[] = [
                'key' => 'test',
                'label' => Craft::t('webform', 'Test Submissions'),
                'criteria' => [
                    'statusType' => 'test',
                ]
            ];
        }

        return $sources;
    }

    protected static function defineActions(string $source = null): array
    {
        return [
            DeleteSubmissions::class,
            MarkSubmissionsAsNew::class,
            MarkSubmissionsAsRead::class,
        ];
    }

    public function getCpEditUrl()
    {
        return UrlHelper::cpUrl('webform/' . $this->id . '?siteId=' . $this->siteId);
    }

    protected function tableAttributeHtml(string $attribute): string
    {
        if ($attribute === 'statusType') {
            return \Craft::$app->view->renderString(
                '<span class="status {{ color }}"></span> {{ label|capitalize }}',
                [
                    'color' => self::$statusColor[$this->statusType],
                    'label' => $this->statusType,
                ]
            );
        }

        return parent::tableAttributeHtml($attribute);
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

        $record->statusType = $this->statusType;
        $record->formHandle = $this->formHandle;
        $record->formTitle = $this->formTitle;
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

    public $statusType;
    public $formHandle;
    public $formTitle;
    public $subject;
    public $recipients;
    public $content;
}
