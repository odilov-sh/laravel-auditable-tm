<?php

namespace OdilovSh\LaravelAuditTm\Resolvers;

class UserIdResolver implements Resolver
{

    /**
     * @return int|null
     */
    public static function resolve()
    {

        if(class_exists('\Seshpulatov\AuthTm\AuthTM')){

            try {
                return  \Seshpulatov\AuthTm\AuthTM::userId();
            }
            catch (\Exception $exception){}
        }

        return null;
    }

}
