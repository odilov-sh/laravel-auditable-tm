<?php

namespace OdilovSh\LaravelAuditTm\Traits;

trait AuditHistory
{
    public function auditHistory()
    {

        $modelClass = get_class($this);
        $modelId = $this->getKey();



    }
}
