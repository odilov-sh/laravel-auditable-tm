<?php

namespace OdilovSh\LaravelAuditTm\Getters;

use Illuminate\Database\Eloquent\Model;
use OdilovSh\LaravelAuditTm\Auditable;
use Ramsey\Collection\Collection;

abstract class Getter
{

    /**
     * @var Model|Auditable
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Override this method to set the old and new values
     * Example:
     * ```php
     *  return [
     *  'old' => [],
     *  'new' => [],
     * ]
     * ```
     *
     * @return void
     */
    abstract public function getValues();

    /**
     * @return array
     */
    public function getTranslatableAttributes(): array
    {
        return $this->model->getTranslatableAttributes();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->old) && empty($this->new);
    }

    /**
     * @return array
     */
    public function getAttributeNewValues(): array
    {

        $result = [];

        $translatableAttributes = $this->getTranslatableAttributes();
        $model = $this->model;

        if (!empty($translatableAttributes)) {

            /** @var Collection|Model[] $translations */
            $translations = $model->translations;
            foreach ($translations as $translation) {
                foreach ($translation->getAttributes() as $attribute => $value) {
                    if (in_array($attribute, $translatableAttributes) && $model->isAttributeAuditable($attribute)) {
                        $result[$attribute . '_' . $translation->locale] = $value;
                    }
                }
            }
        }

        foreach ($model->getAttributes() as $attribute => $value) {
            if ($model->isAttributeAuditable($attribute)) {
                $result[$attribute] = $value;
            }
        }
        return $result;
    }

}
