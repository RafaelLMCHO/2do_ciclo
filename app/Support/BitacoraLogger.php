<?php

namespace App\Support;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BitacoraLogger
{
    public static function log(Request $request, string $accion, ?User $user = null): void
    {
        $inicio = microtime(true);
        $user ??= $request->user();

        if (! $user || trim($accion) === '') {
            Log::info('[PERF] BitacoraLogger::log omitido', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'accion' => $accion,
                'tiene_usuario' => (bool) $user,
            ]);

            return;
        }

        Bitacora::create([
            'id_user' => $user->id_user,
            'fecha_hora' => now(),
            'accion' => $accion,
            'ip' => $request->ip(),
        ]);

        Log::info('[PERF] BitacoraLogger::log create', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'accion' => $accion,
            'id_user' => $user->id_user,
            'route' => $request->route()?->getName(),
        ]);
    }
}
