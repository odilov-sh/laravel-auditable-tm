<?php

namespace OdilovSh\LaravelAuditTm\Getters;

class DeletedEventGetter extends Getter
{

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = $this->getAttributeNewValues();
        return [
            'new' => [],
            'old' => $values,
        ];
    }

}
