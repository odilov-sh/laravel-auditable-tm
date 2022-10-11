<?php

namespace OdilovSh\LaravelAuditTm\Resolvers;

use Seshpulatov\AuthTm\AuthTM;

class UserIdResolver implements Resolver
{

    /**
     * @return int|null
     */
    public static function resolve()
    {
        try {
            $user = AuthTM::user();
            if ($user){
                return $user->id;
            }
        }
        catch (\Exception $exception){
            return null;
        }
        return null;
    }

}
