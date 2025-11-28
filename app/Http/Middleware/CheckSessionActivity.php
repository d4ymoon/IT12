<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; 

class CheckSessionActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== CHECK SESSION MIDDLEWARE RUNNING ===');
    
    $timeout = 10 * 60; // 10 minutes in seconds
    $lastActivity = session('last_activity');
    
    if ($lastActivity && (time() - $lastActivity > $timeout)) {
        Log::info('=== EXPIRING SESSION ===');
        session()->flush();
        return redirect('/login')->with('message', 'Session expired due to inactivity.');
    }
    
    session(['last_activity' => time()]);
    
    return $next($request);
    }
}
