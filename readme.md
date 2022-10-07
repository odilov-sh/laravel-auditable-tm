### About

This package provides a simple way to audit your Eloquent models.
The package collects the following information:

- `service_id` - The service ID of the service that is using the package
- `user_id` - The user who performed the action
- `event` - The event that was performed. The package supports the following events:
    - `created`
    - `updated`
    - `deleted`
- `auditable_type` - The class name of the auditable model
- `auditable_id` - The ID of the auditable model
- `old_values` - The old values of the auditable model
- `new_values` - The new values of the auditable model
- `url` - The URL of the request
- `ip_address` - The IP address of the request
- `user_agent` - The user agent of the request

If you use the `Astrotomic\Translatable\Translatable` trait, the package will also collect the translations of the auditable model, like `name_uz`, `title_uz` and so on.


### Installation

```shell
composer require laravel-audit-tm/audit
```
### Publishing
    
```shell
php artisan vendor:publish --provider="OdilovSh\LaravelAuditTm\AuditTmServiceProvider"
```
### Usage

```php

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    use OdilovSh\LaravelAuditTm\Auditable;

}
```
### Envirement variables
Your .env file must have the following variables:
```dotenv
AUTH_SERVICE_ID=1111
AUDIT_TM_SECRET_KEY="your secret key"
AUDIT_TM_ENABLED=true // if false, audit will not be sent
AUDIT_TM_RECEIVER_URL="your receiver url"
```
### Notes
This package uses `OdilovSh\LaravelAuditTm\Resolvers\UserIdResolver` to resolve the user id. You can change the resolver by changing the `user_id_resolver` in the `audit-tm` config file.
