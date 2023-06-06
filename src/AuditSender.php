<?php

namespace OdilovSh\LaravelAuditTm;

use Http;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
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
            ->post($url, $this->data);

        if (!$result->ok()) {
            $this->error($result->json('message', 'Something went wrong with Audit Service'));
        }
    }

    /**
     * @param string $message
     * @return void
     */
    private function error(string $message)
    {
        Log::error($message);
        session()->flash('error', "AUDIT ERROR! " . $message);
    }

    /**
     * @param array $data
     * @return void
     */
    public static function sendToAudit(array $data): void
    {
        (new AuditSender($data))->send();
    }

}
