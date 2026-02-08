<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = Auth::user();

        $expectedType = constant(UserType::class . '::' . $type);

        if (!$user || $user->user_type->value !== $expectedType) {
            abort(403, "Unauthorized User Type");
        }

        return $next($request);
    }
}
