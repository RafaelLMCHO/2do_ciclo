<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\FuncionalidadController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\PersonalAdministrativoController;

// CU06: Iniciar sesion - registra las rutas base de login de Laravel.
Auth::routes(['register' => false]);

Route::get('admin/password/reset', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('admin/password/email', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'sendResetCode'])->name('admin.password.email');
Route::get('admin/password/reset-form', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset.form');
Route::post('admin/password/update', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'resetPassword'])->name('admin.password.reset.submit');

// CU07: Cerrar sesion - envia la peticion al LoginController@logout.
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/olvido-password', function () {
    return view('auth.olvido');
});

Route::redirect('/dashboard', '/')->middleware('auth');

Route::redirect('/home', '/')->middleware('auth');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home-panel');
    }
    return view('landing');
});

Route::get('/panel', [App\Http\Controllers\HomeController::class, 'index'])->name('home-panel')->middleware('auth');

Route::get('/admin/configuracion', [App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('admin.configuracion.index')->middleware('auth');
Route::post('/admin/configuracion/create', [App\Http\Controllers\Admin\ConfiguracionController::class , 'store'])->name('admin.configuracion.store')->middleware('auth');

// CU03: Gestionar Estudiante - listado de estudiantes.
Route::get('/admin/alumnos', [App\Http\Controllers\Admin\AlumnoController::class, 'index'])->name('admin.alumnos.index')->middleware(['auth', 'can:is-admin']);
// CU03: Gestionar Estudiante - formulario de creacion.
Route::get('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'create'])->name('admin.alumnos.create')->middleware(['auth', 'can:is-admin']);
// CU03 y CU01: Gestionar Estudiante/Usuario - crea alumno y usuario de acceso.
Route::post('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'store'])->name('admin.alumnos.store')->middleware(['auth', 'can:is-admin']);
// CU03: Gestionar Estudiante - formulario de edicion.
Route::get('/admin/alumnos/{id}/edit', [App\Http\Controllers\Admin\AlumnoController::class, 'edit'])->name('admin.alumnos.edit')->middleware(['auth', 'can:is-admin']);
// CU03 y CU01: Gestionar Estudiante/Usuario - actualiza alumno y usuario.
Route::put('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'update'])->name('admin.alumnos.update')->middleware(['auth', 'can:is-admin']);
// CU03 y CU01: Gestionar Estudiante/Usuario - elimina alumno y usuario vinculado.
Route::delete('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'destroy'])->name('admin.alumnos.destroy')->middleware(['auth', 'can:is-admin']);

// CU02: Gestionar Docente - listado de docentes.
Route::get('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'index'])->name('admin.profesores.index')->middleware(['auth', 'can:is-admin']);
// CU02: Gestionar Docente - formulario de creacion.
Route::get('/admin/profesores/create', [App\Http\Controllers\Admin\ProfesorController::class, 'create'])->name('admin.profesores.create')->middleware(['auth', 'can:is-admin']);
// CU02 y CU01: Gestionar Docente/Usuario - crea docente y usuario de acceso.
Route::post('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'store'])->name('admin.profesores.store')->middleware(['auth', 'can:is-admin']);
// CU02 y CU01: Gestionar Docente/Usuario - formulario para editar acceso.
Route::get('/admin/profesores/{id}/edit', [App\Http\Controllers\Admin\ProfesorController::class, 'edit'])->name('admin.profesores.edit')->middleware(['auth', 'can:is-admin']);
// CU02 y CU01: Gestionar Docente/Usuario - actualiza usuario y permiso del docente.
Route::put('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'update'])->name('admin.profesores.update')->middleware(['auth', 'can:is-admin']);
// CU02: Gestionar Docente - formulario para editar informacion personal.
Route::get('/admin/profesores/{id}/edit-info', [App\Http\Controllers\Admin\ProfesorController::class, 'editInfo'])->name('admin.profesores.editInfo')->middleware(['auth', 'can:is-admin']);
// CU02: Gestionar Docente - actualiza informacion personal.
Route::put('/admin/profesores/{id}/info', [App\Http\Controllers\Admin\ProfesorController::class, 'updateInfo'])->name('admin.profesores.updateInfo')->middleware(['auth', 'can:is-admin']);
// CU02 y CU01: Gestionar Docente/Usuario - elimina docente y usuario relacionado.
Route::delete('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'destroy'])->name('admin.profesores.destroy')->middleware(['auth', 'can:is-admin']);

Route::get('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'edit'])->name('admin.password.edit')->middleware('auth');
Route::put('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'update'])->name('admin.password.update')->middleware('auth');

Route::get('/admin/gestiones', [App\Http\Controllers\GestionController::class, 'index'])->name('admin.gestiones.index')->middleware('auth');
Route::get('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'create'])->name('admin.gestiones.create')->middleware('auth');
Route::post('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'store'])->name('admin.gestiones.store')->middleware('auth');
Route::get('/admin/gestiones/{id}/edit', [App\Http\Controllers\GestionController::class, 'edit'])->name('admin.gestiones.edit')->middleware('auth');
Route::put('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'update'])->name('admin.gestiones.update')->middleware('auth');
// CU22: Gestionar Anio Escolar - activa una gestion y desactiva las demas.
Route::put('/admin/gestiones/{id}/activar', [App\Http\Controllers\GestionController::class, 'activar'])->name('admin.gestiones.activar')->middleware('auth');
Route::delete('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'destroy'])->name('admin.gestiones.destroy')->middleware('auth');

// CU10: Gestionar Modulo - CRUD de modulos del sistema.
Route::resource('/admin/modulos', ModuloController::class, ['as' => 'admin'])
    ->except(['show'])
    ->middleware(['auth', 'can:is-admin']);
// CU09: Gestionar Funcionalidad - CRUD de acciones o permisos por modulo.
Route::resource('/admin/funcionalidades', FuncionalidadController::class, ['as' => 'admin'])
    ->except(['show'])
    ->parameters(['funcionalidades' => 'funcionalidad'])
    ->middleware(['auth', 'can:is-admin']);
// CU24: Gestionar Personal Administrativo - CRUD con usuario generado automaticamente.
Route::resource('/admin/personal-administrativo', PersonalAdministrativoController::class, ['as' => 'admin'])
    ->except(['show'])
    ->parameters(['personal-administrativo' => 'personalAdministrativo'])
    ->middleware(['auth', 'can:is-admin']);

Route::get('/admin/cursos', [App\Http\Controllers\Admin\CursoController::class, 'index'])->name('admin.cursos.index')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/cursos/create', [App\Http\Controllers\Admin\CursoController::class, 'create'])->name('admin.cursos.create')->middleware(['auth', 'can:is-admin']);
Route::post('/admin/cursos', [App\Http\Controllers\Admin\CursoController::class, 'store'])->name('admin.cursos.store')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/cursos/{id}/edit', [App\Http\Controllers\Admin\CursoController::class, 'edit'])->name('admin.cursos.edit')->middleware(['auth', 'can:is-admin']);
Route::put('/admin/cursos/{id}', [App\Http\Controllers\Admin\CursoController::class, 'update'])->name('admin.cursos.update')->middleware(['auth', 'can:is-admin']);
Route::delete('/admin/cursos/{id}', [App\Http\Controllers\Admin\CursoController::class, 'destroy'])->name('admin.cursos.destroy')->middleware(['auth', 'can:is-admin']);

Route::get('/admin/materias', [App\Http\Controllers\Admin\MateriaController::class, 'index'])->name('admin.materias.index')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/materias/create', [App\Http\Controllers\Admin\MateriaController::class, 'create'])->name('admin.materias.create')->middleware(['auth', 'can:is-admin']);
Route::post('/admin/materias', [App\Http\Controllers\Admin\MateriaController::class, 'store'])->name('admin.materias.store')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/materias/{id}/edit', [App\Http\Controllers\Admin\MateriaController::class, 'edit'])->name('admin.materias.edit')->middleware(['auth', 'can:is-admin']);
Route::put('/admin/materias/{id}', [App\Http\Controllers\Admin\MateriaController::class, 'update'])->name('admin.materias.update')->middleware(['auth', 'can:is-admin']);
Route::delete('/admin/materias/{id}', [App\Http\Controllers\Admin\MateriaController::class, 'destroy'])->name('admin.materias.destroy')->middleware(['auth', 'can:is-admin']);



// CU05: Gestionar Bitacora - consulta del historial de acciones.
Route::get('/admin/bitacora', [App\Http\Controllers\Admin\BitacoraController::class, 'index'])->name('admin.bitacora.index')->middleware(['auth', 'can:is-admin']);

Route::get('/profesor/horario', [App\Http\Controllers\Profesor\HorarioController::class, 'index'])->name('profesor.horario')->middleware(['auth', 'can:profesor-horario']);

// CU04: Gestionar Tutor - ruta relacionada con el tutor/apoderado para consultar hijos y notas.
Route::get('/apoderado/consulta', [App\Http\Controllers\Apoderado\ConsultaController::class, 'index'])
    ->name('apoderado.consulta')
    ->middleware(['auth', 'can:is-apoderado']);
