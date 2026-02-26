<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StaffAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // If NOT logged in
        if (!auth()->guard('staff')->check()) {
            \Log::info('StaffAuth: User not authenticated', [
                'url' => $request->url(),
                'is_admin_route' => $request->is('admin') || $request->is('admin/*'),
                'is_staff_route' => $request->is('staff') || $request->is('staff/*')
            ]);

            // ✅ Admin URL → Admin Login page
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect('/admin/login');
            }

            // ❌ Everything else → staff login (default)
            return redirect('/staff/login');
        }

        \Log::info('StaffAuth: User authenticated', [
            'user_id' => auth()->guard('staff')->id(),
            'user_email' => auth()->guard('staff')->user()->email
        ]);

        return $next($request);
    }
}
