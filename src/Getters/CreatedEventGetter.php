<?php

namespace OdilovSh\LaravelAuditTm\Getters;

class CreatedEventGetter extends Getter
{

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = $this->getAttributeNewValues();
        return [
            'new' => $values,
            'old' => [],
        ];
    }

}
