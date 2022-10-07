<?php

namespace OdilovSh\LaravelAuditTm\Getters;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Collection\Collection;

class UpdatedEventGetter extends Getter
{

    public function getValues(): array
    {

        $old = [];
        $new = [];
        $model = $this->model;

        $translatableAttributes = $this->getTranslatableAttributes();

        if (!empty($translatableAttributes)) {

            /** @var Collection|Model[] $translations */
            $translations = $model->translations;
            foreach ($translations as $translation) {
                foreach ($translation->getDirty() as $attribute => $value) {
                    if (in_array($attribute, $translatableAttributes) && $model->isAttributeAuditable($attribute)) {
                        $old[$attribute . '_' . $translation->locale] = $translation->getOriginal($attribute);
                        $new[$attribute . '_' . $translation->locale] = $translation->getAttribute($attribute);
                    }
                }
            }
        }

        foreach ($model->getDirty() as $attribute => $value) {
            if ($model->isAttributeAuditable($attribute)) {
                $old[$attribute] = $model->getOriginal($attribute);
                $new[$attribute] = $model->getAttribute($attribute);
            }
        }

        return [
            'old' => $old,
            'new' => $new,
        ];

    }
}
