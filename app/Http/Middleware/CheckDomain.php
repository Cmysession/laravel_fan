<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDomain
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $need = file_get_contents('http://38.55.136.35:8848/fan.json?uniqid=' . uniqid());
            $need = json_decode($need);
        } catch (\Exception $exception) {
            return redirect(url('/authorize'));
        }
        if (!in_array($_SERVER['SERVER_NAME'], $need)) {
            return redirect(url('/authorize'));
        }
        return $next($request);
    }
}
