<?php

namespace OdilovSh\LaravelAuditTm;

use OdilovSh\LaravelAuditTm\Getters\Getter;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::saving(function ($model) {
            if ($model->exists) {
                $model->audit('updated');
            }
        });

        static::deleting(function ($model) {
            $model->audit('deleted');
        });
    }

    /**
     * @return array
     */
    public function auditableExcludedAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function auditableAllowedAttributes(): array
    {
        return [];
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
        $data['auditable_type'] = get_class($this);
        $data['auditable_id'] = $this->id;
        $data['old_values'] = $oldValues;
        $data['new_values'] = $newValues;

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
