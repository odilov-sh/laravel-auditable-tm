<?php

namespace OdilovSh\LaravelAuditTm;

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
     * @return mixed
     */
    public function resolveUserId()
    {
        /** @var Resolver $resolver */
        $resolver = config('audit-tm.user_id_resolver');
        return $resolver::resolve();
    }

    /**
     * @return void
     */
    public function send()
    {
        $url = config('audit-tm.receiver_url');
        $result = \Http::withHeaders([
            'Authorization' => 'Bearer ' . config('audit-tm.secret_key'),
        ])
            ->acceptJson()
            ->get($url, $this->data)
            ->body()
        ;

        $result = json_decode($result, true);

        if (!$result['status'] ) {
            session()->flash('error', "Audit error: {$result['message']}");
        }
    }

}
