<?php

namespace OdilovSh\LaravelAuditTm\Resolvers;

use Seshpulatov\AuthTm\AuthTM;

class UserIdResolver implements Resolver
{

    /**
     * @return mixed
     */
    public static function resolve()
    {
        return AuthTM::user()->id ?? null;
    }

}
