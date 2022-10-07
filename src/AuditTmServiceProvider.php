<?php

namespace OdilovSh\LaravelAuditTm;

use  Illuminate\Support\ServiceProvider;

class AuditTmServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/audit-tm.php' => config_path('audit-tm.php')
        ]);
    }

}
