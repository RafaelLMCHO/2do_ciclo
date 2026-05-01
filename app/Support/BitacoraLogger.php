<?php

namespace App\Support;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraLogger
{
    public static function log(Request $request, string $accion, ?User $user = null): void
    {
        $user ??= $request->user();

        if (! $user || trim($accion) === '') {
            return;
        }

        Bitacora::create([
            'id_user' => $user->id_user,
            'fecha_hora' => now(),
            'accion' => $accion,
            'ip' => $request->ip(),
        ]);
    }
}
