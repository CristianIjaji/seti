<form action="{{ route('users.update_printer', $configuracion) }}" method="POST">
    @csrf
    <div class="input-group mb-3">
        <input type="hidden" name="id_configuracion_cliente" value="{{ $configuracion->id_configuracion_cliente }}">
        <input type="text" class="form-control" placeholder="Nombre Impresora" name="impresora" value="{{ $configuracion->impresora }}" aria-label="Nombre Impresora" aria-describedby="loadPrinters" list="printers">
        <datalist id="printers"></datalist>
        <span class="input-group-text border-0 btn btn-info" id="loadPrinters"><i class="fa-solid fa-rotate-right"></i></span>
    </div>
    @include('partials.buttons', ['create' => false, 'edit' => true, 'label' => 'Actualizar impresora'])
</form>

<script>
    $('#loadPrinters').click(function () {
        obtenerListaDeImpresoras();
    });
</script>