<form action="{{ route('users.update_password', $usuario) }}" method="POST">
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>
    @csrf
    <div class="form-group">
        <label for="password" class="required">Contraseña nueva</label>
        <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" autocomplete="new-password">
    </div>
    <div class="form-group">
        <label for="password-confirm" class="required">Repetir contraseña</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>

    @include('partials.buttons', ['create' => false, 'edit' => true, 'label' => 'Actualizar contraseña'])
</form>