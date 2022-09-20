@php
    $create = isset($consolidado->id_consolidado) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('deals.store') : route('deals.update', $consolidado) }}"></form>
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
            <label for="id_cliente" class="required">Cliente</label>
            @if ($edit)
                <select name="id_cliente" id="id_cliente" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir cliente</option>
                </select>
            @else
                <input type="text" class="form-control" value="{{ $consolidado->tblCliente->full_name }} {{ (isset($consolidado->tblCliente->tblterceroresponsable) ? ' - '.$consolidado->tblCliente->tblterceroresponsable->razon_social : '') }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-date">
            <label for="id_mes">Mes</label>
            <input type="text" name="id_mes" id="id_mes" data-format="YYYY-MM" data-viewMode="months" class="form-control">
        </div>
        {{-- <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="anyo">Año</label>
            @if ($edit)
                <select name="anyo" id="anyo" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir año</option>
                </select>
            @else
                <input type="text" class="form-control" >
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="anyo">Mes</label>
            <select name="id_mes" id="id_mes" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                <option value="">Elegir mes</option>
            </select>
        </div> --}}
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
            <label for="id_responsable_cliente" class="required">Cliente</label>
            @if ($edit)
                <select name="id_responsable_cliente" id="id_responsable_cliente" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir responsable</option>
                </select>
            @else
                <input type="text" class="form-control" value="{{ $consolidado->tblresponsablecliente->full_name }} {{ (isset($consolidado->tblresponsablecliente->tblterceroresponsable) ? ' - '.$consolidado->tblresponsablecliente->tblterceroresponsable->razon_social : '') }}" disabled>
            @endif
        </div>
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear consolidado' : 'Editar consolidado'])

<script type="application/javascript">
    datePicker();
</script>