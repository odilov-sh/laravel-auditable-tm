<?php

namespace OdilovSh\LaravelAuditTm\Traits;

use App;
use OdilovSh\LaravelAuditTm\AuditSender;
use OdilovSh\LaravelAuditTm\Getters\Getter;

trait Auditable
{

    /**
     * @return void
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {

            /** @var self $model */
            $model->audit('created');
        });

        static::saving(function ($model) {

            /** @var self $model */
            if ($model->exists) {
                $model->audit('updated');
            }
        });

        static::deleting(function ($model) {

            /** @var self $model */
            $model->audit('deleted');
        });
    }

    /**
     * These attributes are excluded on auditing
     * @return array
     */
    public function auditableExcludedAttributes(): array
    {
        return [];
    }

    /**
     * These attributes are always audited even if the attribute is excluded globally.
     * @note You can globally exclude attributes in config/audit-tm.php in `exclude` section.
     * @return array
     */
    public function auditableAllowedAttributes(): array
    {
        return [];
    }

    /**
     * Get auditable class name.
     * Default the method returns class name of this object.
     * You can override this method in your model.
     * @return string
     */
    public function getAuditableClassName(): string
    {
        return get_class($this);
    }

    /**
     * Get auditable id.
     * Default the method returns value of 'key' attribute  of this object.
     * You can override this method in your model.
     * @return string|null
     */
    public function getAuditableId(): ?string
    {
        return (string)$this->getKey();
    }

    /**
     * Get auditable tags.
     * You can override this method in your model.
     * @return string|array|null
     */
    public function getAuditableTags(): null|string|array
    {
        return null;
    }

    /**
     * @return array
     */
    public function getTranslatableAttributes(): array
    {
        $classUses = class_uses_recursive($this);
        if (in_array('Astrotomic\Translatable\Translatable', $classUses)) {
            return $this->translatedAttributes;
        }
        return [];
    }

    /**
     * @param string $event
     * @return void
     */
    public function audit(string $event)
    {
        if ($this->isEventAuditable($event)) {
            $this->auditEvent($event);
        }
    }

    /**
     * @param string $event
     * @return bool
     */
    public function isEventAuditable(string $event): bool
    {

        if (App::runningInConsole() && !config('audit-tm.console')) {
            return false;
        }

        $events = config('audit-tm.events', []);
        return in_array($event, $events);
    }

    /**
     * Determine if an attribute is eligible for auditing.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttributeAuditable(string $attribute): bool
    {
        // The attribute should not be audited
        if (in_array($attribute, $this->auditableExcludedAttributes(), true)) {
            return false;
        }

        // The attribute should be audited
        if (in_array($attribute, $this->auditableAllowedAttributes(), true)) {
            return true;
        }

        $globalExcludedAttributes = config('audit-tm.exclude', []);

        // The attribute should not be audited
        if (in_array($attribute, $globalExcludedAttributes, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $event
     * @return void
     */
    public function auditEvent(string $event)
    {

        $values = $this->getAuditableAttributeValues($event);
        $newValues = $values['new'] ?? [];
        $oldValues = $values['old'] ?? [];

        if (!$this->shouldAuditValues($event, $newValues, $oldValues)) {
            return;
        }

        $data = [];
        $data['event'] = $event;
        $data['auditable_type'] = $this->getAuditableClassName();
        $data['auditable_id'] = $this->getAuditableId();
        $data['old_values'] = $oldValues;
        $data['new_values'] = $newValues;

        $tags = $this->getAuditableTags();

        if ($tags){
            if (is_array($tags)){
                $tags = implode(', ', $tags);
            }
            $data['tags'] = $tags;
        }

        (new AuditSender($data))->send();

    }

    /**
     * @param string $event
     * @param array $newValues
     * @param array $oldValues
     * @return bool
     */
    public function shouldAuditValues(string $event, array $newValues, array $oldValues): bool
    {
        if (!empty($newValues) || !empty($oldValues)) {
            return true;
        }

        if (config('audit-tm.audit_empty_values', false)) {
            return true;
        }

        $allowed_empty_values = config('audit-tm.allowed_empty_values', []);

        if (in_array($event, $allowed_empty_values)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $event
     * @return array[]
     *
     * @see Getter
     */
    public function getAuditableAttributeValues(string $event): array
    {

        $getters = config('audit-tm.getters', []);
        if (isset($getters[$event])) {
            /** @var Getter $getter */
            $getter = $getters[$event];
            return (new $getter($this))->getValues();
        }

        return [
            'old' => [],
            'new' => [],
        ];
    }

}
