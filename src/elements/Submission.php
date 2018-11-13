<?php
/**
 * PerForm plugin for Craft CMS 3.x
 *
 * Online form builder and submissions
 *
 * @link      https://perfectus.us
 * @copyright Copyright (c) 2018 Perfectus Digital Solutions
 */

namespace perfectus\perform\elements;

use perfectus\perform\PerForm;

use perfectus\perform\records\SubmissionRecord;
use perfectus\perform\elements\actions\DeleteSubmissions;
use perfectus\perform\elements\actions\MarkSubmissionsAsNew;
use perfectus\perform\elements\actions\MarkSubmissionsAsRead;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use craft\web\ErrorHandler;

/**
 * @author    Oto Hlincik
 * @package   PerForm
 * @since     1.0.0
 *
 * @property string $name
 */
class Submission extends Element
{
    // Public Properties
    // =========================================================================

    /**
     * @var string Status of form submission
     *
     * Available: new, read, test
     */
    public $statusType;

    /**
     * @var string Form Handle of form submission
     */
    public $formHandle;

    /**
     * @var string Title of submission
     */
    public $formTitle;

    /**
     * @var string Subject of form submission
     */
    public $subject;

    /**
     * @var string Recipients of form submission
     *
     * Comma separated email addresses
     */
    public $recipients;

    /**
     * @var string The email that has been captured from submission as 'Reply To'
     */
    public $replyTo;

    /**
     * @var string Serialized content of form submission
     */
    public $content;

    // Static Properties
    // =========================================================================

    /**
     * @var array Colors associated with status types
     */
    public static $statusColor = [
        'new'     => 'green',
        'read'    => 'grey',
        'test'    => 'orange',
    ];

    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('perform', 'Submission');
    }

    /**
     * @inheritdoc
     */
    public static function refHandle()
    {
        return 'submission';
    }

    /**
     * @inheritdoc
     */
    public static function isLocalized(): bool
    {
        return true;
    }

    /**
     * Submission query to relate plugin data to elements
     *
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new SubmissionQuery(static::class);
    }

    // Protected Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected static function defineTableAttributes(): array
    {
        return [
            'subject' => \Craft::t('perform', 'Subject'),
            'statusType' => \Craft::t('perform', 'Status'),
            'dateCreated' => \Craft::t('perform', 'Submitted'),
            'formTitle' => \Craft::t('perform', 'Form Title'),
            'formHandle' => \Craft::t('perform', 'Form Handle'),
        ];
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    protected static function defineSearchableAttributes(): array
    {
        return [
            'subject',
            'formTitle',
            'formHandle',
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        return [
            [
                'label' => Craft::t('perform', 'Submitted'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated'
            ],
            'subject' => \Craft::t('perform', 'Subject'),
            'statusType' => \Craft::t('perform', 'Status'),
            'formTitle' => \Craft::t('perform', 'Form Title'),
            'formHandle' => \Craft::t('perform', 'Form Handle'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => Craft::t('perform', 'All Submissions'),
                'criteria' => []
            ],
            [
                'key' => 'new',
                'label' => Craft::t('perform', 'NEW Submissions'),
                'criteria' => [
                    'statusType' => 'new',
                ]
            ],
            [
                'key' => 'read',
                'label' => Craft::t('perform', 'Read Submissions'),
                'criteria' => [
                    'statusType' => 'read',
                ]
            ],
        ];

        $testSubmissionsCount = PerForm::$plugin->formService->getSubmissionsCount('test');

        // Include the test submissions source only if any test submissions exist
        if ($testSubmissionsCount > 0) {
            $sources[] = [
                'key' => 'test',
                'label' => Craft::t('perform', 'Test Submissions'),
                'criteria' => [
                    'statusType' => 'test',
                ]
            ];
        }

        return $sources;
    }

    /**
     * @inheritdoc
     */
    protected static function defineActions(string $source = null): array
    {
        return [
            DeleteSubmissions::class,
            MarkSubmissionsAsNew::class,
            MarkSubmissionsAsRead::class,
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string)$this->subject;
    }

    /**
     * @inheritdoc
     */
    public function getCpEditUrl()
    {
        return UrlHelper::cpUrl('perform/' . $this->id . '?siteId=' . $this->siteId);
    }

    /**
     * @inheritdoc
     */
    public function afterSave(bool $isNew)
    {
        if (!$isNew) {
            $record = SubmissionRecord::findOne($this->id);
            if (!$record) {
                throw new \RuntimeException('Invalid submission ID: '.$this->id);
            }
        } else {
            $record = new SubmissionRecord();
            $record->id = $this->id;
        }

        $record->statusType = $this->statusType;
        $record->formHandle = $this->formHandle;
        $record->formTitle  = $this->formTitle;
        $record->subject    = $this->subject;
        $record->recipients = $this->recipients;
        $record->replyTo    = $this->replyTo;
        $record->content    = $this->content;
        $record->save(false);

        parent::afterSave($isNew);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        try {
            return $this->getName();
        } catch (\Throwable $e) {
            ErrorHandler::convertExceptionToError($e);
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
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
}
