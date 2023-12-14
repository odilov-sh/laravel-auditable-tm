### About

This package provides a simple way to audit your Eloquent models.
The package collects the following information:

- `service_id` - The ID of the service that is using the package
- `user_id` - The user who performed the action
- `event` - The event that was performed.
- `auditable_type` - The class name of the auditable model. Defaults to model class name. You can override `getAuditableClassName()` in your model.
- `auditable_id` - The ID of the auditable model. You can override `getAuditableId()` in your model.
- `old_values` - The old values of the auditable model
- `new_values` - The new values of the auditable model
- `tags` - Additional tags. You can override `getAuditableTags()` in your model.
- `url` - The URL of the request
- `ip_address` - The IP address of the request
- `user_agent` - The user agent of the request

If you use the `Astrotomic\Translatable\Translatable` trait, the package will also collect the translations of the auditable model, like `name_uz`, `title_uz` and so on.


### Installation

```shell
composer require odilov-sh/laravel-audit-tm
```
### Publishing
    
```shell
php artisan vendor:publish --provider="OdilovSh\LaravelAuditTm\AuditTmServiceProvider"
```
### Envirement variables
Your .env file must have the following variables:
```dotenv
AUTH_SERVICE_ID=1111
AUDIT_TM_SECRET_KEY="your secret key"
AUDIT_TM_ENABLED=true // if false, audit will not be sent
AUDIT_TM_BASE_URL="audit receiver base url"
```

### Usage

```php

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    use OdilovSh\LaravelAuditTm\Traits\Auditable;

}

```

The Auditable trait will automatically send data to audit server on `updated`, `created` and `deleted` events. In other cases, you must send data manually. Manually send data might be like this:

```php

// Manually send data to audit server

use OdilovSh\LaravelAuditTm\AuditSender;

$data = [];
$data['event'] = 'changeStatus'; // Event name
$data['auditable_type'] = 'App\Models\Product'; // The name of the class being audited
$data['auditable_id'] = 123; // The id of the model being audited

// Old values before changing
$data['old_values'] = [
    'status' => 'active'
];

// New values after changing
$data['new_values'] = [
    'status' => 'inactive'
];

(new AuditSender($data))->send();
```

### Queue
You can use queue job to send auditing data to audit server. In this case, you will not wait sending request to audit server and getting response. All of processes will be done in background. To use queue, you just need add new configs to env file
```dotenv
AUDIT_TM_QUEUE_IS_ENABLED=true
AUDIT_TM_ON_QUEUE=default
```

### Notes
This package uses `OdilovSh\LaravelAuditTm\Resolvers\UserIdResolver` to resolve the user id. You can change the resolver by changing the `user_id_resolver` in the `audit-tm` config file. As well you can set `false` this configuration. In this case `user_id` value will not be sent. 

