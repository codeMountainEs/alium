<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmpresaAccess
{
    public function handle(Request $request, Closure $next)
    {
        $task = $request->route('task');
        
        if ($task && !auth()->user()->is_admin && $task->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para acceder a esta tarea.');
        }

        return $next($request);
    }
} 