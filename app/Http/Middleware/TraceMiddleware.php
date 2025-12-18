<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use App\Models\RequestAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TraceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $request->headers->get('X-Trace-Id') ?? (String) Str::uuid();
        
        // store trace + start time for terminate()
        $request->attributes->set('trace_id',$traceId);
        $request->attributes->set('start_time',microtime(true));
        Log::info('MW BEFORE',[
            'trace_id' => $traceId,
            'path' => $request->path(),
            'method' => $request->method(),
        ]);

        // pass request forward
        $response =  $next($request);

        //AFTER route/controller (but before response is sent fully)
        $response->headers->set('X-Trace-Id',$traceId);

        Log::info('MW AFTER',[
            'trace_id' => $traceId,
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }

    public function terminate(Request $request, $response){
        //AFTER response sent (cleanup phase)
        $traceId = $request->attributes->get('trace_id')
        ?? $response->headers->get('X-Trace-Id');
        $start = $request->attributes->get('start_time');
        $durationMS = $start ? (int) round((microtime(true) - $start)*1000):null;

        //Audit Records
        try {
            RequestAudit::create([
                'trace_id' => $traceId,
                'user_id' => optional($request->user())->id,
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $durationMS,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(),0,2000),
            ]);
            Log::info('MW TERMINATE',[
                'trace_id' => $traceId,
                'path' => $request->path(),
            ]);
        } catch(\Throwable $e){
            // terminate must never crash the request lifecycle
            Log::error('MW TERMINATE (audit failed)',[
                'trace_id' => $traceId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
