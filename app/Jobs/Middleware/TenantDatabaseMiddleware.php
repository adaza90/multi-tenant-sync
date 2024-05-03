<?php

namespace App\Jobs\Middleware;

use App\Models\JobExecutionLog;
use Closure;
use Illuminate\Support\Facades\DB;

class TenantDatabaseMiddleware
{
    /**
     * Process the queued job.
     *
     * @param  \Closure(object): void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        // Set Database Connection
        $this->setDatabaseConnection($job);

        $logInfo = $this->prepareLogInfo($job);

        try {
            $response = $next($job);
            $logInfo['success'] = true;
            $logInfo['response'] = $response;
        } catch (\Throwable $e) {
            $logInfo['success'] = false;
            $logInfo['response'] = $e->getMessage();
            $job->fail();
        }

        JobExecutionLog::create($logInfo);

        // Disconnect Database
        $this->disconnectDatabase($job);
    }

    private function prepareLogInfo($job)
    {
        return [
            'job_execution_id' => $job->jobExecutionId,
            'tenant_name' => $job->database['db_database'],
        ];
    }

    private function setDatabaseConnection($job)
    {
        clearDatabaseConn('tenant', $job->hash);
        setDatabaseConn('tenant', $job->database, $job->hash);
    }

    private function disconnectDatabase($job)
    {
        DB::disconnect('tenant'.$job->hash);
        DB::purge('tenant'.$job->hash);
    }
}
