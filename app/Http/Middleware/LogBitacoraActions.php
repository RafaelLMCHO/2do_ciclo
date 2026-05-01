<?php

namespace App\Http\Middleware;

use App\Enums\Rol;
use App\Models\User;
use App\Support\BitacoraLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogBitacoraActions
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user() || $response->getStatusCode() >= 400) {
            return $response;
        }

        $accion = $this->resolveAction($request);

        if ($accion) {
            BitacoraLogger::log($request, $accion, $request->user());
        }

        return $response;
    }

    private function resolveAction(Request $request): ?string
    {
        $routeName = $request->route()?->getName();
        $user = $request->user();

        if (!$routeName || !$user) {
            return null;
        }

        $rolLabel = Rol::tryFrom((int) $user->id_rol)?->label() ?? 'Usuario';
        $esAdmin = (int) $user->id_rol === Rol::ADMIN->value;

        return match ($routeName) {
            'home' => $rolLabel . ' accedio al panel principal',

            'admin.configuracion.index' => 'Administrador consulto la configuracion del sistema',
            'admin.configuracion.store' => 'Administrador actualizo la configuracion del sistema',
            'admin.password.edit' => 'Administrador abrio la pantalla de cambio de contrasena',
            'admin.password.update' => 'Administrador cambio su contrasena',

            'admin.alumnos.index' => 'Administrador consulto el listado de alumnos',
            'admin.alumnos.create' => 'Administrador abrio el formulario de registro de alumnos',
            'admin.alumnos.store' => 'Administrador registro un alumno',
            'admin.alumnos.edit' => 'Administrador abrio la edicion de un alumno',
            'admin.alumnos.update' => 'Administrador actualizo los datos de un alumno',
            'admin.alumnos.destroy' => 'Administrador elimino un alumno',

            'admin.profesores.index' => 'Administrador consulto el listado de profesores',
            'admin.profesores.edit' => 'Administrador abrio la configuracion de acceso de un profesor',
            'admin.profesores.update' => 'Administrador actualizo el acceso de un profesor',

            'admin.gestiones.index' => 'Administrador consulto las gestiones',
            'admin.gestiones.create' => 'Administrador abrio el formulario de gestiones',
            'admin.gestiones.store' => 'Administrador registro una gestion',
            'admin.gestiones.edit' => 'Administrador abrio la edicion de una gestion',
            'admin.gestiones.update' => 'Administrador actualizo una gestion',
            'admin.gestiones.destroy' => 'Administrador elimino una gestion',

            'admin.nivels.index' => 'Administrador consulto los niveles',
            'admin.nivels.store' => 'Administrador registro un nivel',
            'admin.nivels.update' => 'Administrador actualizo un nivel',
            'admin.nivels.destroy' => 'Administrador elimino un nivel',

            'admin.turnos.index' => 'Administrador consulto los turnos',
            'admin.turnos.create' => 'Administrador abrio el formulario de turnos',
            'admin.turnos.store' => 'Administrador registro un turno',
            'admin.turnos.edit' => 'Administrador abrio la edicion de un turno',
            'admin.turnos.update' => 'Administrador actualizo un turno',
            'admin.turnos.destroy' => 'Administrador elimino un turno',

            'admin.bitacora.index' => 'Administrador consulto la bitacora',

            'profesor.horario' => $esAdmin
                ? 'Administrador consulto el horario de profesores'
                : 'Profesor consulto su horario',

            'apoderado.consulta' => $esAdmin
                ? 'Administrador consulto las notas de los alumnos'
                : 'Apoderado consulto las notas de sus hijos',

            default => null,
        };
    }
}
