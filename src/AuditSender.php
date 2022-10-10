<?php

namespace OdilovSh\LaravelAuditTm;

use Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use OdilovSh\LaravelAuditTm\Resolvers\Resolver;

class AuditSender
{

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $data['service_id'] = config('audit-tm.service_id');
        $data['user_id'] = $this->resolveUserId();
        $data['user_agent'] = Request::header('User-Agent');
        $data['ip_address'] = Request::ip();
        $data['url'] = App::runningInConsole() ? 'console' : Request::fullUrl();
        $this->data = $data;
    }

    /**
     * @return int|null
     */
    public function resolveUserId()
    {
        /** @var Resolver $resolver */
        $resolver = config('audit-tm.user_id_resolver');
        return $resolver ? $resolver::resolve() : null;
    }

    /**
     * @return void
     */
    public function send()
    {
        $url = config('audit-tm.receiver_url') . '/api/audit-receive';
        $token = config('audit-tm.secret_key');
        $result = Http::withToken($token)
            ->acceptJson()
            ->get($url, $this->data)
            ->body();

        $result = json_decode($result, true);

        $status = $result['status'] ?? false;
        if (!$status) {
            session()->flash('error', "Audit error: {$result['message']}");
        }
    }

}
