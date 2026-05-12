{{-- CU24: Captura datos personales y laborales del personal administrativo. --}}
<div class="row">
<!--     <div class="col-md-4">
        <div class="form-group">
            <label>CI <span class="text-danger">*</span></label>
            <input type="text" name="ci" class="form-control" value="{{ old('ci', $personalAdministrativo->ci ?? '') }}" required>
            @error('ci') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div> -->
    <div class="col-md-4">
        <div class="form-group">
            <label>Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $personalAdministrativo->nombre ?? '') }}" required>
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Apellido paterno <span class="text-danger">*</span></label>
            <input type="text" name="ap_paterno" class="form-control" value="{{ old('ap_paterno', $personalAdministrativo->ap_paterno ?? '') }}" required>
            @error('ap_paterno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Apellido materno</label>
            <input type="text" name="ap_materno" class="form-control" value="{{ old('ap_materno', $personalAdministrativo->ap_materno ?? '') }}">
            @error('ap_materno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Telefono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $personalAdministrativo->telefono ?? '') }}">
            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Direccion</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $personalAdministrativo->direccion ?? '') }}">
            @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
<div class="row">
<!--     <div class="col-md-4">
        <div class="form-group">
            <label>Cargo <span class="text-danger">*</span></label>
            <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $personalAdministrativo->cargo ?? '') }}" required>
            @error('cargo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div> -->
<!--     <div class="col-md-4">
        <div class="form-group">
            <label>Area <span class="text-danger">*</span></label>
            <input type="text" name="area" class="form-control" value="{{ old('area', $personalAdministrativo->area ?? '') }}" required>
            @error('area') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de ingreso <span class="text-danger">*</span></label>
            <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', isset($personalAdministrativo) ? optional($personalAdministrativo->fecha_ingreso)->format('Y-m-d') : '') }}" required>
            @error('fecha_ingreso') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div> -->
</div>

@if (! isset($personalAdministrativo))
    <hr>
    <h5 class="mb-3 text-success">
        <i class="fas fa-key mr-1"></i>
        Datos de acceso al sistema
    </h5>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Usuario <span class="text-danger">*</span></label>
                <input type="text"
                       name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}"
                       required>
                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Contraseña <span class="text-danger">*</span></label>
                <input type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password"
                       name="password_confirmation"
                       class="form-control"
                       required>
            </div>
        </div>
    </div>
@else
    <hr>
    <h5 class="mb-3 text-success">
        <i class="fas fa-key mr-1"></i>
        Datos de acceso al sistema
    </h5>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Usuario <span class="text-danger">*</span></label>
                <input type="text"
                       name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', optional($personalAdministrativo->usuario)->username) }}"
                       required>
                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar nueva contraseña</label>
                <input type="password"
                       name="password_confirmation"
                       class="form-control">
            </div>
        </div>
    </div>
@endif
