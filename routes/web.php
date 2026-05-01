<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\NivelController;
use App\Http\Controllers\Admin\TurnoController;

Auth::routes(['register' => false]);

Route::get('admin/password/reset', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('admin/password/email', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'sendResetCode'])->name('admin.password.email');
Route::get('admin/password/reset-form', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset.form');
Route::post('admin/password/update', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'resetPassword'])->name('admin.password.reset.submit');

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

Route::get('/admin/alumnos', [App\Http\Controllers\Admin\AlumnoController::class, 'index'])->name('admin.alumnos.index')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'create'])->name('admin.alumnos.create')->middleware(['auth', 'can:is-admin']);
Route::post('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'store'])->name('admin.alumnos.store')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/alumnos/{id}/edit', [App\Http\Controllers\Admin\AlumnoController::class, 'edit'])->name('admin.alumnos.edit')->middleware(['auth', 'can:is-admin']);
Route::put('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'update'])->name('admin.alumnos.update')->middleware(['auth', 'can:is-admin']);
Route::delete('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'destroy'])->name('admin.alumnos.destroy')->middleware(['auth', 'can:is-admin']);

Route::get('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'index'])->name('admin.profesores.index')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/profesores/create', [App\Http\Controllers\Admin\ProfesorController::class, 'create'])->name('admin.profesores.create')->middleware(['auth', 'can:is-admin']);
Route::post('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'store'])->name('admin.profesores.store')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/profesores/{id}/edit', [App\Http\Controllers\Admin\ProfesorController::class, 'edit'])->name('admin.profesores.edit')->middleware(['auth', 'can:is-admin']);
Route::put('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'update'])->name('admin.profesores.update')->middleware(['auth', 'can:is-admin']);
Route::get('/admin/profesores/{id}/edit-info', [App\Http\Controllers\Admin\ProfesorController::class, 'editInfo'])->name('admin.profesores.editInfo')->middleware(['auth', 'can:is-admin']);
Route::put('/admin/profesores/{id}/info', [App\Http\Controllers\Admin\ProfesorController::class, 'updateInfo'])->name('admin.profesores.updateInfo')->middleware(['auth', 'can:is-admin']);
Route::delete('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'destroy'])->name('admin.profesores.destroy')->middleware(['auth', 'can:is-admin']);

Route::get('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'edit'])->name('admin.password.edit')->middleware('auth');
Route::put('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'update'])->name('admin.password.update')->middleware('auth');

Route::get('/admin/gestiones', [App\Http\Controllers\GestionController::class, 'index'])->name('admin.gestiones.index')->middleware('auth');
Route::get('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'create'])->name('admin.gestiones.create')->middleware('auth');
Route::post('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'store'])->name('admin.gestiones.store')->middleware('auth');
Route::get('/admin/gestiones/{id}/edit', [App\Http\Controllers\GestionController::class, 'edit'])->name('admin.gestiones.edit')->middleware('auth');
Route::put('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'update'])->name('admin.gestiones.update')->middleware('auth');
Route::delete('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'destroy'])->name('admin.gestiones.destroy')->middleware('auth');

Route::get('/admin/niveles', [NivelController::class, 'index'])->name('admin.nivels.index')->middleware('auth');
Route::post('/admin/niveles/create', [NivelController::class, 'store'])->name('admin.nivels.store')->middleware('auth');
Route::put('/admin/niveles/{id}', [NivelController::class, 'update'])->name('admin.nivels.update')->middleware('auth');
Route::delete('/admin/niveles/{id}', [NivelController::class, 'destroy'])->name('admin.nivels.destroy')->middleware('auth');

Route::get('/admin/turnos', [TurnoController::class, 'index'])->name('admin.turnos.index')->middleware('auth');
Route::get('/admin/turnos/create', [TurnoController::class, 'create'])->name('admin.turnos.create')->middleware('auth');
Route::post('/admin/turnos/create', [TurnoController::class, 'store'])->name('admin.turnos.store')->middleware('auth');
Route::get('/admin/turnos/{id}/edit', [TurnoController::class, 'edit'])->name('admin.turnos.edit')->middleware('auth');
Route::put('/admin/turnos/{id}', [TurnoController::class, 'update'])->name('admin.turnos.update')->middleware('auth');
Route::delete('/admin/turnos/{id}', [TurnoController::class, 'destroy'])->name('admin.turnos.destroy')->middleware('auth');

Route::get('/admin/bitacora', [App\Http\Controllers\Admin\BitacoraController::class, 'index'])->name('admin.bitacora.index')->middleware(['auth', 'can:is-admin']);

Route::get('/profesor/horario', [App\Http\Controllers\Profesor\HorarioController::class, 'index'])->name('profesor.horario')->middleware(['auth', 'can:profesor-horario']);

Route::get('/apoderado/consulta', [App\Http\Controllers\Apoderado\ConsultaController::class, 'index'])
    ->name('apoderado.consulta')
    ->middleware(['auth', 'can:is-apoderado']);