{{-- CU24: Captura datos personales y laborales del personal administrativo. --}}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>CI <span class="text-danger">*</span></label>
            <input type="text" name="ci" class="form-control" value="{{ old('ci', $personalAdministrativo->ci ?? '') }}" required>
            @error('ci') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
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
    <div class="col-md-4">
        <div class="form-group">
            <label>Cargo <span class="text-danger">*</span></label>
            <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $personalAdministrativo->cargo ?? '') }}" required>
            @error('cargo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
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
    </div>
</div>
